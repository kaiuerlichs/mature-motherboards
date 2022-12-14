<?php

require_once(dirname(__FILE__) . "/libraries/payment/PaymentConnector.php");

class Database
{
    private $host = "silva.computing.dundee.ac.uk";
    private $db_name = "22ac3d05";
    private $username = "";
    private $password = "";
    private $connection;

    function __construct($dbuser = "default")
    {
        $db_access = fopen(dirname(__FILE__) . "/db_access.txt", "r");
        $credentials = array();

        while (!feof($db_access)) {
            $tokens = explode(":", fgets($db_access));
            $credentials[$tokens[0]] = $tokens[1];
        }

        $this->username = trim($credentials[$dbuser]);
        $this->password = trim($credentials["password"]);
    }

    function connect()
    {
        try {
            $this->connection = new PDO(
                'mysql:host=' . $this->host . ';dbname=' . $this->db_name,
                $this->username,
                $this->password
            );
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        } catch (PDOException $e) {
            echo "Connection error: " . $e->getMessage();
            die();
        }
    }

    function GetPasswordHash($emailIn)
    {
        $q = $this->connection->prepare("CALL GetPasswordHash(:emailIn)");
        $q->bindParam(":emailIn", $emailIn);

        $q->execute();

        foreach ($q->fetchAll() as $result) {
            return $result["password"];
        }

        throw new InvalidArgumentException("Invalid email.");
    }

    function debug_to_console($data) {
        $output = $data;
        if (is_array($output))
            $output = implode(',', $output);
    
        echo "<script>console.log('Debug Objects: " . $output . "' );</script>";
    }

    function GetEmployeeData($emailIn)
    {
        $q = $this->connection->prepare("SELECT RoleID, EmployeeID FROM employee WHERE Email = :emailIn");
        $q->bindParam(":emailIn", $emailIn);

        $q->execute();

        foreach ($q->fetchAll() as $result) {
            return [$result["RoleID"], $result["EmployeeID"]];
        }

        throw new InvalidArgumentException("Invalid email.");
    }

    function InputOrder($email, $productID, $charge){
        // Start new transaction
        if (!$this->connection->beginTransaction()) {
            error_log("Error starting transaction.");
            throw new PDOException("Error starting transaction.", 1);
        }
        $paymentref = "Cash Payment";
        $addrID = 12;
        try {
            $q = $this->connection->prepare("INSERT INTO `order` (DatePlaced, Email, AddressID, charge, PaymentReference) VALUES (CURDATE(), :email, :addrID, :charge, :payRef);");
            $q->bindParam(":email", $email);
            $q->bindParam(":addrID", $addrID );
            $q->bindParam(":charge", $charge );
            $q->bindParam(":payRef", $paymentref );

            if (!$q->execute()) {
                throw new PDOException();
            }

            // Query for new order ID (no user input -> query is safe)
            foreach ($this->connection->query("SELECT LAST_INSERT_ID() AS 'id'") as $result) {
                $ordID = $result["id"];
            }
        } catch (PDOException $e) {
            error_log($e);
            error_log("Error creating order.");
            $this->connection->rollBack();
            throw new PDOException("Error creating order.", 3);
        }

        // // Get each item by ID and check availability, add price to total
        $totalAmount = 0;

        
        try {
            $q = $this->connection->prepare("SELECT Price, Stock FROM product WHERE ProductID = :id FOR UPDATE;");
            $q->bindParam(":id", $productID);

            if (!$q->execute()) {
                throw new PDOException();
            }

            foreach ($q->fetchAll() as $result) {
                if ($result["Stock"] > 0) {
                    $totalAmount += $result["Price"];

                    // Update stock
                    $q = $this->connection->prepare("UPDATE product SET Stock = Stock - 1 WHERE ProductID = :prodID;");
                    $q->bindParam(":prodID", $productID);
                    if (!$q->execute()) {
                        throw new PDOException();
                    }

                    // Add row to joining table
                    $q = $this->connection->prepare("INSERT INTO `order contains product` (ProductID, OrderID) VALUES (:prodID, :ordID);");
                    $q->bindParam(":prodID", $productID);
                    $q->bindParam(":ordID", $ordID);
                    if (!$q->execute()) {
                        throw new PDOException();
                    }
                } else {
                    error_log("Not enough stock.");
                    $this->connection->rollBack();
                    throw new PDOException("Not enough stock for " . $productID . ".", 4);
                }
            }
        } catch (PDOException $e) {
            error_log($e);
            error_log("Error calculating total.");
            $this->connection->rollBack();
            throw new PDOException("Error calculating total.", 5);
        }
         // Commit transaction
         if (!$this->connection->commit()) {
            error_log("Error committing transaction.");
            $this->connection->rollBack();
            throw new PDOException("Error committing transaction.", 7);
        }
        return $ordID;
    }

    

    function CreateOrder($products, $orderDetails, $cardDetails, $addressDetails)
    {
        // Start new transaction
        if (!$this->connection->beginTransaction()) {
            error_log("Error starting transaction.");
            throw new PDOException("Error starting transaction.", 1);
        }

        // Create Shipping Address
        try {
            // Prepare statement to insert new address into db
            $q = $this->connection->prepare("INSERT INTO address (name, Address1, Address2, Address3, Town, PostCode) VALUES (:name, :addr1, :addr2, :addr3, :town, :postcode);");
            $name = $orderDetails["firstname"] . ' ' . $orderDetails["lastname"];
            $q->bindParam(":name", $name);
            $q->bindParam(":addr1", $addressDetails["line1"]);
            $q->bindParam(":addr2", $addressDetails["line2"]);
            $q->bindParam(":addr3", $addressDetails["line3"]);
            $q->bindParam(":town", $addressDetails["town"]);
            $q->bindParam(":postcode", $addressDetails["postcode"]);

            if (!$q->execute()) {
                throw new PDOException();
            }

            // Query for new address ID (no user input -> query is safe)
            foreach ($this->connection->query("SELECT LAST_INSERT_ID() AS 'id'") as $result) {
                $addrID = $result["id"];
            }
        } catch (PDOException $e) {
            error_log("Error creating shipping address.");
            $this->connection->rollBack();
            throw new PDOException("Error creating shipping address.", 2);
        }

        // Create Order
        try {
            $q = $this->connection->prepare("INSERT INTO `order` (DatePlaced, Email, AddressID) VALUES (CURDATE(), :email, :addrID);");
            $q->bindParam(":email", $orderDetails["email"]);
            $q->bindParam(":addrID", $addrID);

            if (!$q->execute()) {
                throw new PDOException();
            }

            // Query for new order ID (no user input -> query is safe)
            foreach ($this->connection->query("SELECT LAST_INSERT_ID() AS 'id'") as $result) {
                $ordID = $result["id"];
            }
        } catch (PDOException $e) {
            error_log("Error creating order.");
            $this->connection->rollBack();
            throw new PDOException("Error creating order.", 3);
        }

        // // Get each item by ID and check availability, add price to total
        $totalAmount = 0;

        foreach ($products as $prodID) {
            try {
                $q = $this->connection->prepare("SELECT Price, Stock FROM product WHERE ProductID = :id FOR UPDATE;");
                $q->bindParam(":id", $prodID);

                if (!$q->execute()) {
                    throw new PDOException();
                }

                foreach ($q->fetchAll() as $result) {
                    if ($result["Stock"] > 0) {
                        $totalAmount += $result["Price"];

                        // Update stock
                        $q = $this->connection->prepare("UPDATE product SET Stock = Stock - 1 WHERE ProductID = :prodID;");
                        $q->bindParam(":prodID", $prodID);
                        if (!$q->execute()) {
                            throw new PDOException();
                        }

                        // Add row to joining table
                        $q = $this->connection->prepare("INSERT INTO `order contains product` (ProductID, OrderID) VALUES (:prodID, :ordID);");
                        $q->bindParam(":prodID", $prodID);
                        $q->bindParam(":ordID", $ordID);
                        if (!$q->execute()) {
                            throw new PDOException();
                        }
                    } else {
                        error_log("Not enough stock.");
                        $this->connection->rollBack();
                        throw new PDOException("Not enough stock for " . $prodID . ".", 4);
                    }
                }
            } catch (PDOException $e) {
                error_log("Error calculating total.");
                $this->connection->rollBack();
                throw new PDOException("Error calculating total.", 5);
            }
        }

        // Processing payment
        try {
            $ref = makePayment($cardDetails["name"], $cardDetails["number"], $cardDetails["cvv"], $cardDetails["expiry"], $totalAmount);
        } catch (Exception $e) {
            error_log("Error processing payment.");
            $this->connection->rollBack();
            throw new ErrorException("Error processing payment.", 6);
        }

        // Update order with payment info
        try {
            $q = $this->connection->prepare("UPDATE `order` SET PaymentReference = :ref, charge = :charge WHERE OrderID = :ordID;");
            $q->bindParam(":ref", $ref);
            $q->bindParam(":ordID", $ordID);
            $q->bindParam(":charge", $totalAmount);

            if (!$q->execute()) {
                throw new PDOException();
            }
        } catch (PDOException $e) {
            error_log("Error updating payment info.");
            $this->connection->rollBack();
            throw new PDOException("Error updating payment info.", 7);
        }

        // Commit transaction
        if (!$this->connection->commit()) {
            error_log("Error committing transaction.");
            $this->connection->rollBack();
            throw new PDOException("Error committing transaction.", 7);
        }

        return $ordID;
    }

    function GetAllBranches()
    {
        // Start new transaction
        if (!$this->connection->beginTransaction()) {
            error_log("Error starting transaction.");
            throw new PDOException("Error starting transaction.", 1);
        }

        // Get branch information
        try {
            $q = $this->connection->prepare("
                SELECT b.BranchID, b.Name, a.name AS 'AddressName' ,a.Address1, a.Address2, a.Address3, a.Town, a.PostCode, o.WeekDay, o.OpenTime, o.CloseTime
                FROM branch b
                JOIN address a on a.AddressID = b.AddressID
                JOIN openinghours o on b.BranchID = o.BranchID;
            ");

            if (!$q->execute()) {
                throw new PDOException();
            }

            $table = $q->fetchAll();
        } catch (PDOException $e) {
            error_log("Error creating order.");
            $this->connection->rollBack();
            throw new PDOException("Error creating order.", 3);
        }

        // Commit transaction
        if (!$this->connection->commit()) {
            error_log("Error committing transaction.");
            $this->connection->rollBack();
            throw new PDOException("Error committing transaction.", 3);
        }

        return $table;
    }

    function GetBranchById($id)
    {
        // Start new transaction
        if (!$this->connection->beginTransaction()) {
            error_log("Error starting transaction.");
            throw new PDOException("Error starting transaction.", 1);
        }

        // Get branch information
        try {
            $q = $this->connection->prepare("
                SELECT b.BranchID, b.Name, a.name AS 'AddressName' ,a.Address1, a.Address2, a.Address3, a.Town, a.PostCode, o.WeekDay, o.OpenTime, o.CloseTime
                FROM branch b
                JOIN address a on a.AddressID = b.AddressID
                JOIN openinghours o on b.BranchID = o.BranchID
                WHERE b.BranchID = :id;
            ");
            $q->bindParam(":id", $id);

            if (!$q->execute()) {
                throw new PDOException();
            }

            $table = $q->fetchAll();
        } catch (PDOException $e) {
            error_log("Error creating order.");
            $this->connection->rollBack();
            throw new PDOException("Error creating order.", 3);
        }

        // Commit transaction
        if (!$this->connection->commit()) {
            error_log("Error committing transaction.");
            $this->connection->rollBack();
            throw new PDOException("Error committing transaction.", 3);
        }

        return $table;
    }

    function getProductsList()
    {
        
        // Start new transaction
        if (!$this->connection->beginTransaction()) {
            error_log("Error starting transaction.");
            throw new PDOException("Error starting transaction.", 1);
        }

        // Get item information
        try {
            $q = $this->connection->prepare("SELECT ProductID, Price, Type, Name, Image FROM product WHERE Stock > 0;");
            if (!$q->execute()) {
                throw new PDOException();
            }
            $itemtable = $q->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error creating order.");
            $this->connection->rollBack();
            throw new PDOException("Error creating order.", 3);
        }

        // Commit transaction
        if (!$this->connection->commit()) {
            error_log("Error committing transaction.");
            $this->connection->rollBack();
            throw new PDOException("Error committing transaction.", 3);
        }
        return $itemtable;
    }
    function getProductByID($prodID)
    {

        // Start new transaction
        if (!$this->connection->beginTransaction()) {
            error_log("Error starting transaction.");
            throw new PDOException("Error starting transaction.", 1);
        }

        // Get item information
        try {
            $q = $this->connection->prepare("SELECT Price, Type, Name, Description, Stock, ProductionDate, OperatingSystem, Architecture, Storage, PageCount, Image FROM product WHERE ProductID = :prodID;");
            $q->bindParam(":prodID", $prodID);
            if (!$q->execute()) {
                throw new PDOException();
            }
            $iteminfo = $q->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error creating order.");
            $this->connection->rollBack();
            throw new PDOException("Error creating order.", 3);
        }

        // Commit transaction
        if (!$this->connection->commit()) {
            error_log("Error committing transaction.");
            $this->connection->rollBack();
            throw new PDOException("Error committing transaction.", 3);
        }
        return $iteminfo;
    }
    function scheduleRepairByEmployee($scheduleDetails, $employeeID)
    {
        // Start new transaction
        if (!$this->connection->beginTransaction()) {
            error_log("Error starting transaction.");
            throw new PDOException("Error starting transaction.", 1);
        }

        // Create repair slot
        try {
            $ts = date("Y-m-d H:i:s", strtotime($scheduleDetails["Time"]));
            // Prepare statement to insert repair into db
            $q = $this->connection->prepare("INSERT INTO repairs (BranchID, Time,
            Duration, Description, Email, FirstName,
            LastName, DatePlaced)
            VALUES ((SELECT BranchId from employee WHERE EmployeeID = :emplID), :time, :duration, :description, :email,
            :firstname, :lastname, CURDATE());");
            $q->bindParam(":emplID", $employeeID);
            $q->bindParam(":time", $ts);
            $q->bindParam(":duration", $scheduleDetails["Duration"]);
            $q->bindParam(":description", $scheduleDetails["Description"]);
            $q->bindParam(":email", $scheduleDetails["Email"]);
            $q->bindParam(":firstname", $scheduleDetails["firstname"]);
            $q->bindParam(":lastname", $scheduleDetails["lastname"]);

            if (!$q->execute()) {
                throw new PDOException();
            }

            // Query for new repair ID (no user input -> query is safe)
            foreach ($this->connection->query("SELECT LAST_INSERT_ID() AS 'id'") as $result) {
                $scheduleID = $result["id"];
            }

            // Commit transaction
            if (!$this->connection->commit()) {
                error_log("Error committing transaction.");
                $this->connection->rollBack();
                throw new PDOException("Error committing transaction.", 3);
            }
        } catch (PDOException $e) {
            throw $e;
            error_log("Error creating repair.");
            $this->connection->rollBack();
            throw new PDOException("Error creating repair slot.", 2);
        }
        return $scheduleID;
    }

    function scheduleRepair($scheduleDetails)
    {
        // Start new transaction
        if (!$this->connection->beginTransaction()) {
            error_log("Error starting transaction.");
            throw new PDOException("Error starting transaction.", 1);
        }

        // Create repair slot
        try {
            $ts = date("Y-m-d H:i:s", strtotime($scheduleDetails["Time"]));
            // Prepare statement to insert repair into db
            $q = $this->connection->prepare("INSERT INTO repairs (BranchID, Time, Duration, Description, Email, FirstName, LastName, DatePlaced) VALUES (:branchid, :time, :duration, :description, :email, :firstname, :lastname, CURDATE());");
            $q->bindParam(":branchid", $scheduleDetails["branchId"]);
            $q->bindParam(":time", $ts);
            $q->bindParam(":duration", $scheduleDetails["Duration"]);
            $q->bindParam(":description", $scheduleDetails["Description"]);
            $q->bindParam(":email", $scheduleDetails["Email"]);
            $q->bindParam(":firstname", $scheduleDetails["firstname"]);
            $q->bindParam(":lastname", $scheduleDetails["lastname"]);

            if (!$q->execute()) {
                throw new PDOException();
            }

            // Query for new repair ID (no user input -> query is safe)
            foreach ($this->connection->query("SELECT LAST_INSERT_ID() AS 'id'") as $result) {
                $scheduleID = $result["id"];
            }

            // Commit transaction
            if (!$this->connection->commit()) {
                error_log("Error committing transaction.");
                $this->connection->rollBack();
                throw new PDOException("Error committing transaction.", 3);
            }
        } catch (PDOException $e) {
            throw $e;
            error_log("Error creating repair.");
            $this->connection->rollBack();
            throw new PDOException("Error creating repair slot.", 2);
        }
        return $scheduleID;
    }

    function GetShiftsByID($employeeID)
    {

        // Start new transaction
        if (!$this->connection->beginTransaction()) {
            error_log("Error starting transaction.");
            throw new PDOException("Error starting transaction.", 1);
        }

        // Get item information
        try {
            $q = $this->connection->prepare("
                SELECT s.Start, s.End
                FROM shift s
                WHERE EmployeeID = :emplID;
            ");
            $q->bindParam(":emplID", $employeeID);
            if (!$q->execute()) {
                throw new PDOException();
            }
            $iteminfo = $q->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error creating order.");
            $this->connection->rollBack();
            throw new PDOException("Error finding shifts.", 3);
        }

        // Commit transaction
        if (!$this->connection->commit()) {
            error_log("Error committing transaction.");
            $this->connection->rollBack();
            throw new PDOException("Error committing transaction.", 3);
        }
        return $iteminfo;
    }

    function GetShifts($userid)
    {
        // Start new transaction
        if (!$this->connection->beginTransaction()) {
            error_log("Error starting transaction.");
            throw new PDOException("Error starting transaction.", 1);
        }

        // Get item information
        try {
            $q = $this->connection->prepare("
                SELECT Start, End, employee.EmployeeID, FirstName, LastName
                FROM shift
                JOIN employee on shift.EmployeeID = employee.EmployeeID
                WHERE BranchID = (SELECT BranchID FROM employee WHERE EmployeeID = :emplID);
            ");
            $q->bindParam(":emplID", $userid);
            if (!$q->execute()) {
                throw new PDOException();
            }
            $iteminfo = $q->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error creating order.");
            $this->connection->rollBack();
            throw new PDOException("Error finding shifts.", 3);
        }

        // Commit transaction
        if (!$this->connection->commit()) {
            error_log("Error committing transaction.");
            $this->connection->rollBack();
            throw new PDOException("Error committing transaction.", 3);
        }
        return $iteminfo;
    }
    
    function addProduct($productinfo){

        //Start new transaction
        if (!$this->connection->beginTransaction()) {
            error_log("Error starting transaction.");
            throw new PDOException("Error starting transaction.", 1);
        }
        foreach ($productinfo as $prod){
            error_log($prod);
        }
        
       
        // Create Order
        try {
            $q = $this->connection->prepare("INSERT INTO `product` (Name, Description, Type, Price, Stock, Image, ProductionDate, Architecture, OperatingSystem, PageCount) VALUES (:name, :desc, :type, :price, :stock, :img, :date, :arch, :os, :pgCount);");
            
            $q->bindParam(":name", $productinfo["Name"]);
            $q->bindParam(":desc", $productinfo["Desc"]);
            $q->bindParam(":type", $productinfo["Type"]);
            $q->bindParam(":stock", $productinfo["Stock"]);
            $q->bindParam(":img", $productinfo["Image"]);
            $q->bindParam(":price", $productinfo["Price"]);
            $q->bindParam(":date", $productinfo["ProductionDate"]);
            $q->bindParam(":arch", $productinfo["Architecture"]);
            $q->bindParam(":os", $productinfo["OperatingSystem"]);
            $q->bindParam(":pgCount", $productinfo["PageCount"]);
            

            if (!$q->execute()) {
                throw new PDOException();
            }

            // Query for new order ID (no user input -> query is safe)
            foreach ($this->connection->query("SELECT LAST_INSERT_ID() AS 'id'") as $result) {
                $prodRef = $result["id"];
            }
        } catch (PDOException $e) {
            error_log($e);
            error_log("Error creating order.");
            $this->connection->rollBack();
            throw new PDOException("Error creating order.", 3);
        }
        //Commit transaction
        if (!$this->connection->commit()) {
            error_log("Error committing transaction.");
            $this->connection->rollBack();
            throw new PDOException("Error committing transaction.", 3);
        }
        return $prodRef;
    }

    function addShift($shiftDetails, $employeeID) {
        //Start new transaction
        if (!$this->connection->beginTransaction()) {
            error_log("Error starting transaction.");
            throw new PDOException("Error starting transaction.", 1);
        }

        //Create shift
        try {
            //Prepare statement to insert shift into employee works shift
            $q = $this->connection->prepare("INSERT INTO shift (Start, End, EmployeeID) VALUES (:start, :end, :employeeid);");
            $q->bindParam(":start", date("Y-m-d H:i:s", strtotime($shiftDetails["startTime"])));
            $q->bindParam(":end", date("Y-m-d H:i:s", strtotime($shiftDetails["endTime"])));
            $q->bindParam(":employeeid", $employeeID);

            if (!$q->execute()) {
                throw new PDOException();
            }

            //Query for new shift ID (no user input -> query is safe)
            foreach ($this->connection->query("SELECT LAST_INSERT_ID() AS 'id'") as $result) {
                $shiftID = $result["id"];
            }
        } catch(PDOException $e) {
            error_log("Error creating shift.");
            $this->connection->rollBack();
            throw new PDOException("Error creating shift.", 2);
        }
        //Commit transaction
        if (!$this->connection->commit()) {
            error_log("Error committing transaction.");
            $this->connection->rollBack();
            throw new PDOException("Error committing transaction.", 3);
        }

        
        return $shiftID;
    }
    function getRepairsByBranch($userid){
        if (!$this->connection->beginTransaction()) {
            error_log("Error starting transaction.");
            throw new PDOException("Error starting transaction.", 1);
        }

        try {
            $q = $this->connection->prepare("
                SELECT r.RepairID, r.Time, r.Duration, r.Description, r.Email, r.Status, r.Firstname, r.Lastname, r.DatePlaced
                FROM repairs r
                JOIN employee e on r.BranchID = e.BranchID
                WHERE e.EmployeeID = :emplID;
            ");
            $q->bindParam(":emplID", $userid);
            if (!$q->execute()) {
                throw new PDOException();
            }
            $repairinfo = $q->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error creating order.");
            $this->connection->rollBack();
            throw new PDOException("Error finding shifts.", 3);
        }
        // Commit transaction
        if (!$this->connection->commit()) {
            error_log("Error committing transaction.");
            $this->connection->rollBack();
            throw new PDOException("Error committing transaction.", 3);
        }
        return $repairinfo;
    }

    function GetRepairById($id){
        if (!$this->connection->beginTransaction()) {
            error_log("Error starting transaction.");
            throw new PDOException("Error starting transaction.", 1);
        }

        try {
            $q = $this->connection->prepare("
                SELECT RepairID, r.BranchID, b.Name AS BranchName, Time, Duration, Description, Email, Status, FirstName, LastName, DatePlaced
                FROM repairs r
                JOIN branch b on r.BranchID = b.BranchID
                WHERE RepairID = :repairID
            ");
            $q->bindParam(":repairID", $id);
            if (!$q->execute()) {
                throw new PDOException();
            }
            $repairinfo = $q->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error creating order.");
            $this->connection->rollBack();
            throw new PDOException("Error finding shifts.", 3);
        }

        // Commit transaction
        if (!$this->connection->commit()) {
            error_log("Error committing transaction.");
            $this->connection->rollBack();
            throw new PDOException("Error committing transaction.", 3);
        }
        return $repairinfo;
    }

    function GetOrderById($id){
        if (!$this->connection->beginTransaction()) {
            error_log("Error starting transaction.");
            throw new PDOException("Error starting transaction.", 1);
        }

        try {
            $q = $this->connection->prepare("
                SELECT OrderID, DatePlaced, Email, charge, a.name, a.Address1, a.Address2, a.Address3, a.Town, a.PostCode
                FROM `order`
                JOIN address a on a.AddressID = `order`.AddressID
                WHERE OrderID = :ordID;
            ");
            $q->bindParam(":ordID", $id);
            if (!$q->execute()) {
                throw new PDOException();
            }
            $orderinfo = $q->fetch(PDO::FETCH_ASSOC);

            $q = $this->connection->prepare("
                SELECT p.Name, p.Image, p.Price
                FROM `order contains product` ocp
                JOIN product p on ocp.ProductID = p.ProductID
                WHERE OrderID = :ordID;
            ");
            $q->bindParam(":ordID", $id);
            if (!$q->execute()) {
                throw new PDOException();
            }
            $orderinfo["products"] = $q->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error finding order data.");
            $this->connection->rollBack();
            throw new PDOException("Error finding order data.", 3);
        }

        // Commit transaction
        if (!$this->connection->commit()) {
            error_log("Error committing transaction.");
            $this->connection->rollBack();
            throw new PDOException("Error committing transaction.", 3);
        }
        return $orderinfo;
    }

    function getAllEmployees(){
        // Start new transaction
        if (!$this->connection->beginTransaction()) {
            error_log("Error starting transaction.");
            throw new PDOException("Error starting transaction.", 1);
        }

        // Get item information
        try {
            $q = $this->connection->prepare("SELECT EmployeeID, BranchID, FirstName, LastName, Email, Phone FROM employee;");
            if (!$q->execute()) {
                throw new PDOException();
            }
            $employeeinfo = $q->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error creating order.");
            $this->connection->rollBack();
            throw new PDOException("Error creating order.", 3);
        }

        // Commit transaction
        if (!$this->connection->commit()) {
            error_log("Error committing transaction.");
            $this->connection->rollBack();
            throw new PDOException("Error committing transaction.", 3);
        }
        return $employeeinfo;
    }

    function UpdateRepairStatus($rid, $status){
        // Start new transaction
        if (!$this->connection->beginTransaction()) {
            error_log("Error starting transaction.");
            throw new PDOException("Error starting transaction.", 1);
        }

        try {
            $q = $this->connection->prepare("SELECT Status FROM repairs WHERE RepairID = :rID FOR UPDATE;");
            $q->bindParam(":rID", $rid);
            if (!$q->execute()) {
                throw new PDOException();
            }

            $q = $this->connection->prepare("UPDATE repairs SET Status = :status WHERE RepairID = :rID;");
            $q->bindParam(":rID", $rid);
            $q->bindParam(":status", $status);
            if (!$q->execute()) {
                throw new PDOException();
            }
        } catch (PDOException $e) {
            error_log("Error updating status.");
            $this->connection->rollBack();
            throw new PDOException("Error updating status.", 3);
        }

        // Commit transaction
        if (!$this->connection->commit()) {
            error_log("Error committing transaction.");
            $this->connection->rollBack();
            throw new PDOException("Error committing transaction.", 3);
        }
    }
    function IncreaseStock($stock, $prodID){
        // Start new transaction
        if (!$this->connection->beginTransaction()) {
            error_log("Error starting transaction.");
            throw new PDOException("Error starting transaction.", 1);
        }
        try {
            $q = $this->connection->prepare("SELECT Stock FROM product WHERE ProductID = :prodID FOR UPDATE;");
            $q->bindParam(":prodID", $prodID);
            if (!$q->execute()) {
                throw new PDOException();
            }
            $q = $this->connection->prepare("UPDATE product SET Stock = Stock + :stock WHERE ProductID = :prodID;");
            $q->bindParam(":prodID", $prodID);
            $q->bindParam(":stock", $stock);
            if (!$q->execute()) {
                throw new PDOException();
            }
        } catch (PDOException $e) {
            error_log($e);
            error_log("Error updating status.");
            $this->connection->rollBack();
            throw new PDOException("Error updating status.", 3);
        }

        // Commit transaction
        if (!$this->connection->commit()) {
            error_log("Error committing transaction.");
            $this->connection->rollBack();
            throw new PDOException("Error committing transaction.", 3);
        }
        return $prodID;
    }
}
