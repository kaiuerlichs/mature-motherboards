<?php
require_once("../Database.php");

$db = new Database();
$db->connect();

header('Content-Type: application/json; charset=utf-8');

try{
    $table = $db->GetAllBranches();

    $result = array();

    foreach($table as $row){
        if(!isset($result[$row["BranchID"]])){
            $result[$row["BranchID"]] = array(
                "name" => $row["Name"],
                "address" => array(
                    "addressName" => $row["AddressName"],
                    "line1" => $row["Address1"],
                    "line2" => $row["Address2"],
                    "line3" => $row["Address3"],
                    "town" => $row["Town"],
                    "postcode" => $row["PostCode"],
                ),
                "hours" => array()
            );
        }

        switch($row["WeekDay"]){
            case 0:
                $day = "mon";
                break;
            case 1:
                $day = "tue";
                break;
            case 2:
                $day = "wed";
                break;
            case 3:
                $day = "thu";
                break;
            case 4:
                $day = "fri";
                break;
            case 5:
                $day = "sat";
                break;
            case 6:
                $day = "sun";
                break;
        }

        $result[$row["BranchID"]]["hours"][$day] = array($row["OpenTime"], $row["CloseTime"]);
    }

    echo json_encode($result);
}
catch(PDOException $e){
    // Processing errors
    echo json_encode(array(
        "error" => "Error retrieving branch information.",
        "code" => 401
    ));
    return;
}