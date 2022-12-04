<?php

require_once(  dirname(__FILE__) . "/PaymentConnector.php");

class Database
{
    private $host = "silva.computing.dundee.ac.uk";
    private $db_name = "22ac3d05";
    private $username = "";
    private $password = "";
    private $connection;

    function __construct($dbuser = "default")
    {
        $db_access = fopen( dirname(__FILE__) . "/db_access.txt", "r");
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
            $this->connection->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
            $this->connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
        } catch (PDOException $e) {
            echo $this->username;
            echo $this->password;
            die();
        }
    }

    function GetPasswordHash($emailIn)
    {
        $this->connection->beginTransaction();
        $q = $this->connection->prepare("CALL GetPasswordHash(:emailIn)");
        $q->bindParam(":emailIn", $emailIn);

        $q->execute();

        $this->connection->commit();

        foreach ($q->fetchAll() as $result) {
            return $result["password"];
        }

        throw new InvalidArgumentException("Invalid email.");
    }

    function GetEmployeeRole($emailIn)
    {
        $this->connection->beginTransaction();
        $q = $this->connection->prepare("CALL GetEmployeeRole(:emailIn)");
        $q->bindParam(":emailIn", $emailIn);

        $q->execute();

        $this->connection->commit();

        foreach ($q->fetchAll() as $result) {
            return $result["roleID"];
        }

        throw new InvalidArgumentException("Invalid email.");
    }

    function CreateOrder($products, $orderDetails ,$cardDetails, $addressDetails)
    {
        // Start new transaction
        if(!$this->connection->beginTransaction()){
            error_log("Error starting transaction.");
            throw new PDOException("Error starting transaction.", 1);
        }

        // Create Shipping Address
        try{
            // Prepare statement to insert new address into db
            $q = $this->connection->prepare("INSERT INTO address (name, Address1, Address2, Address3, Town, PostCode) VALUES (:name, :addr1, :addr2, :addr3, :town, :postcode);");
            $name = $orderDetails["firstname"] . ' ' . $orderDetails["lastname"];
            $q->bindParam(":name", $name);
            $q->bindParam(":addr1", $addressDetails["line1"]);
            $q->bindParam(":addr2", $addressDetails["line2"]);
            $q->bindParam(":addr3", $addressDetails["line3"]);
            $q->bindParam(":town", $addressDetails["town"]);
            $q->bindParam(":postcode", $addressDetails["postcode"]);

            if (!$q->execute()){
                throw new PDOException();
            }

            // Query for new address ID (no user input -> query is safe)
            foreach($this->connection->query("SELECT LAST_INSERT_ID() AS 'id'") as $result){
                $addrID = $result["id"];
            }
        }
        catch(PDOException $e){
            error_log("Error creating shipping address.");
            $this->connection->rollBack();
            throw new PDOException("Error creating shipping address.", 2);
        }

        // Create Order
        try{
            $q = $this->connection->prepare("INSERT INTO `order` (DatePlaced, Email, AddressID) VALUES (CURDATE(), :email, :addrID);");
            $q->bindParam(":email", $orderDetails["email"]);
            $q->bindParam(":addrID", $addrID);

            if (!$q->execute()){
                throw new PDOException();
            }

            // Query for new order ID (no user input -> query is safe)
            foreach($this->connection->query("SELECT LAST_INSERT_ID() AS 'id'") as $result){
                $ordID = $result["id"];
            }
        }
        catch(PDOException $e){
            error_log("Error creating order.");
            $this->connection->rollBack();
            throw new PDOException("Error creating order.", 3);
        }

        // // Get each item by ID and check availability, add price to total
        $totalAmount = 0;

        foreach ($products as $prodID) {
            try{
                $q = $this->connection->prepare("SELECT Price, Stock FROM product WHERE ProductID = :id FOR UPDATE;");
                $q->bindParam(":id", $prodID);
                
                if (!$q->execute()){
                    throw new PDOException();
                }

                foreach ($q->fetchAll() as $result) {
                    if($result["Stock"] > 0){
                        $totalAmount += $result["Price"];
                        
                        // Update stock
                        $q = $this->connection->prepare("UPDATE product SET Stock = Stock - 1 WHERE ProductID = :prodID;");
                        $q->bindParam(":prodID", $prodID);
                        if (!$q->execute()){
                            throw new PDOException();
                        }

                        // Add row to joining table
                        $q = $this->connection->prepare("INSERT INTO `order contains product` (ProductID, OrderID) VALUES (:prodID, :ordID);");
                        $q->bindParam(":prodID", $prodID);
                        $q->bindParam(":ordID", $ordID);
                        if (!$q->execute()){
                            throw new PDOException();
                        }
                    }
                    else{
                        error_log("Not enough stock.");
                        $this->connection->rollBack();
                        throw new PDOException("Not enough stock for " . $prodID . ".", 4);
                    }
                }
            }
            catch(PDOException $e){
                error_log("Error calculating total.");
                $this->connection->rollBack();
                throw new PDOException("Error calculating total.", 5);
            }
        }

        // Processing payment
        try{
            $ref = makePayment($cardDetails["name"], $cardDetails["number"], $cardDetails["cvv"], $cardDetails["expiry"], $totalAmount);
        }
        catch(Exception $e){
            error_log("Error processing payment.");
            $this->connection->rollBack();
            throw new ErrorException("Error processing payment.", 6);
        }

        // Update order with payment info
        try{
            $q = $this->connection->prepare("UPDATE `order` SET PaymentReference = :ref, charge = :charge WHERE OrderID = :ordID;");
            $q->bindParam(":ref", $ref);
            $q->bindParam(":ordID", $ordID);
            $q->bindParam(":charge", $totalAmount);

            if (!$q->execute()){
                throw new PDOException();
            }
        }
        catch(PDOException $e){
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
}