<?php
//php -S localhost:8000

require_once(__DIR__ . "/../Database.php");

$db = new Database();
$db->connect();

header('Content-Type: application/json; charset=utf-8');

$_POST = json_decode(file_get_contents('php://input'), true);
$topLevelKeys = ["shiftDetails", "employeeID"];
$shiftKeys = ["startTime", "endTime"];

foreach ($topLevelKeys as $key) {
    if(!isset($_POST[$key])){
        // Missing keys
        echo json_encode(array(
            "error" => "Missing keys.",
            "code" => 301
        ));
        return;
    }
}
foreach($shiftKeys as $key){
    if(!isset($_POST["shiftDetails"][$key])){
        // Missing keys
        echo json_encode(array(
            "error" => "Missing keys in shiftDetails.",
            "code" => 302
        ));
        return;
    }
}
try {
    $shiftNumber = $db->addShift($_POST);
    echo json_encode(array(
        "success" => "Shift added successfully.",
        "code" => 300,
        "shiftNumber" => $shiftNumber
    ));
}
catch (PDOException $e) {
    echo json_encode(array(
        "error" => "Internal processing errror.",
        "code" => 305
    ));
    return;
}