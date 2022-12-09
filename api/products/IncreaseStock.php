

<?php
require_once("../Database.php");


//name desc type price stock, Image, architecture, os, noOfPages

$db = new Database();
$db->connect();

header('Content-Type: application/json; charset=utf-8');
session_start();
$_POST = json_decode(file_get_contents('php://input'), true);
if (!isset($_POST["stock"])){
    echo json_encode(array(
        "error" => "No stock added",
        "code" => 304
    ));
    return;
}
if (!isset($_POST["productID"])){
    echo json_encode(array(
        "error" => "No stock added",
        "code" => 304
    ));
    return;
}
try{
    $productNumber = $db->IncreaseStock($_POST["stock"], $_POST["productID"]);

    // Order created successfully
    echo json_encode(array(
        "message" => "Stock increased successfully.",
        "prodID" => $productNumber,
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