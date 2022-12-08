<?php
require_once(__DIR__ . "/../Database.php");

$db = new Database();
$db->connect();

header('Content-Type: application/json; charset=utf-8');

try{
    $repair = $db->GetRepairById($_GET["id"]);

    // Order created successfully
    echo json_encode($repair);
}
catch(PDOException $e){
    // Processing errors
    echo json_encode(array(
        "error" => "Internal error retrieving repair data.",
        "code" => 305
    ));
    return;
}