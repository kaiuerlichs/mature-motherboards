
<?php
require_once("../Database.php");


//name desc type price stock, Image, architecture, os, noOfPages

$db = new Database();
$db->connect();

header('Content-Type: application/json; charset=utf-8');
session_start();
$_POST = json_decode(file_get_contents('php://input'), true);

if ($_SESSION['permissions'] != 1){
    //not a manager
    echo json_encode(array(
        "error" => "Not logged in as a manager",
        "code" => 304
    ));
    return;
}
if (!isset($_POST['Name'])){
    // Missing keys
    echo json_encode(array(
        "error" => "Missing name.",
        "code" => 304
    ));
    return;
}
if (!isset($_POST['Desc'])){
    // Missing keys
    echo json_encode(array(
        "error" => "Missing description",
        "code" => 304
    ));
    return;
}
if (!isset($_POST['Type'])){
    // Missing keys
    echo json_encode(array(
        "error" => "Missing type.",
        "code" => 304
    ));
    return;
}
if (!isset($_POST['Stock'])){
    // Missing keys
    echo json_encode(array(
        "error" => "Missing stock.",
        "code" => 304
    ));
    return;
}
if (!isset($_POST['Image'])){
    // Missing keys
    echo json_encode(array(
        "error" => "Missing Image.",
        "code" => 304
    ));
    return;
}
if (!isset($_POST['ProductionDate'])){
    $_POST['ProductionDate'] = NULL;
}
if (!isset($_POST['Architecture'])) {
    $_POST['Architecture'] = "";
}
if (!isset($_POST['OperatingSystem'])) {
    $_POST['OperatingSystem']= "";
}
if ($_POST['PageCount']=="") {
    $_POST['PageCount'] = 0;
}

try{
    $productNumber = $db->AddProduct($_POST);

    // Order created successfully
    echo json_encode(array(
        "message" => "Product added successfully.",
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