<?php
require_once(__DIR__ . "/../Database.php");

$db = new Database();
$db->connect();

header('Content-Type: application/json; charset=utf-8');

try{
    $order = $db->GetOrderById($_GET["id"]);

    // Order created successfully
    echo json_encode($order);
}
catch(PDOException $e){
    error_log($e);
    // Processing errors
    echo json_encode(array(
        "error" => "Internal error retrieving order data.",
        "code" => 305
    ));
    return;
}