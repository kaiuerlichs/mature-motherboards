<?php
require_once("../Database.php");
require_once("../libraries/mailing/Mailer.php");

$db = new Database();
$db->connect();

header('Content-Type: application/json; charset=utf-8');

$_POST = json_decode(file_get_contents('php://input'), true);

$topLevelKeys = ["orderDetails", "addressDetails", "cardDetails", "products"];
$orderKeys = ["firstname", "lastname", "email"];
$addressKeys = ["line1", "line2", "line3", "town", "postcode"];
$cardKeys = ["name", "number", "cvv", "expiry", "line1", "line2", "line3", "town", "postcode"];

foreach($topLevelKeys as $key){
    if(!isset($_POST[$key])){
        // Missing keys
        echo json_encode(array(
            "error" => "Missing keys.",
            "code" => 301
        ));
        return;
    }
}

foreach($addressKeys as $key){
    if(!isset($_POST["addressDetails"][$key])){
        // Missing keys
        echo json_encode(array(
            "error" => "Missing keys in addressDetails.",
            "code" => 302
        ));
        return;
    }
}

foreach($orderKeys as $key){
    if(!isset($_POST["orderDetails"][$key])){
        // Missing keys
        echo json_encode(array(
            "error" => "Missing keys in orderDetails.",
            "code" => 303
        ));
        return;
    }
}

foreach($cardKeys as $key){
    if(!isset($_POST["cardDetails"][$key])){
        // Missing keys
        echo json_encode(array(
            "error" => "Missing keys in cardDetails.",
            "code" => 304
        ));
        return;
    }
}

try{
    $orderNumber = $db->CreateOrder($_POST["products"], $_POST["orderDetails"], $_POST["cardDetails"], $_POST["addressDetails"]);

    // Order created successfully
    echo json_encode(array(
        "message" => "Order created successfully.",
        "ordID" => $orderNumber,
        "code" => 300
    ));

    Mailer::sendOrderConfirmation($_POST["orderDetails"]["firstname"], $_POST["orderDetails"]["lastname"], $_POST["orderDetails"]["email"], $orderNumber);
}
catch(PDOException $e){
    // Processing errors
    echo json_encode(array(
        "error" => "Internal error processing order.",
        "code" => 305
    ));
    return;
}