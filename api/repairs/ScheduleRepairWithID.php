<?php
require_once("../Database.php");
require_once("../libraries/mailing/Mailer.php");

$db = new Database();
$db->connect();

header('Content-Type: application/json; charset=utf-8');

$_POST = json_decode(file_get_contents('php://input'), true);

$keys = ["branchId", "Time", "Duration", "Description", "Email", "firstname", "lastname"];

foreach ($keys as $element) {
    if(!isset($_POST[$element])){
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

    Mailer::sendRepairConfirmation($_POST["firstname"], $_POST["lastname"], $_POST["Email"], $scheduleID);
}
catch(PDOException $e){
    // Processing errors
    echo json_encode(array(
        "error" => "Internal error processing order.",
        "code" => 305
    ));
    return;
}
