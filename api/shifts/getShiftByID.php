<?php
require_once(__DIR__ . "/../Database.php");

$db = new Database();
$db->connect();



header('Content-Type: application/json; charset=utf-8');


try{
    if(!isset($_Session["loggedIn"]){
        throw new Exception("Not logged in");
    }
    if(!isset($_Get["id"]){
        throw new Exception("Not employee ID given");
    }
    $item = $db->getShiftByID($_GET["id"]);

    // Order created successfully
    echo json_encode($item);
}
catch(PDOException $e){
    // Processing errors
    echo json_encode(array(
        "error" => "Internal error processing order.",
        "code" => 305
    ));
    return;
}