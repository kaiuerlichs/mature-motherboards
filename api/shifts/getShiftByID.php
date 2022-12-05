<?php
require_once(__DIR__ . "/../Database.php");

$db = new Database();
$db->connect();

session_start();

header('Content-Type: application/json; charset=utf-8');


try{
    if(!isset($_SESSION["loggedIn"])){
        throw new Exception("Not logged in");
    }
    if(!isset($_GET["id"])){
        throw new Exception("Not employee ID given");
    }
    $shift = $db->getShiftsByID($_GET["id"]);

    // Order created successfully
    echo json_encode($shift);
}
catch(PDOException $e){
    // Processing errors
    echo json_encode(array(
        "error" => "Internal error processing order.",
        "code" => 305
    ));
    return;
}