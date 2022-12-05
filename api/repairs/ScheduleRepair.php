<?php
require_once("../Database.php");

$db = new Database();
$db->connect();

header('Content-Type: application/json; charset=utf-8');

$_POST = json_decode(file_get_contents('php://input'), true);

//format of post request branchId, Time, Duration, Description, Email, firstname, lastname

foreach ($_POST as $element) {
    if(!isset($element)){
        // Missing keys
        echo json_encode(array(
            "error" => "Missing keys.",
            "code" => 301
        ));
        return;
    }

}

try{
    $scheduleID = $db->scheduleRepair($_POST);

    // Order created successfully
    echo json_encode(array(
        "message" => "Repair successfully scheduled.",
        "ordID" => $scheduleID,
        "code" => 300
    ));
}
catch(PDOException $e){
    // Processing errors
    echo json_encode(array(
        "error" => "Internal error processing order.",
        "code" => 305
    ));
    return;
}
