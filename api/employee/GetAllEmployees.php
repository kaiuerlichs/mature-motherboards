<?php
require_once(__DIR__ . "/../Database.php");

$db = new Database();
$db->connect();

session_start();

if(!isset($_SESSION["loggedIn"])){
    // User not logged in
    echo json_encode(
        array(
            "error" => "User not logged in.",
            "code" => 200
        )
    );
    return;
}
if ($_SESSION['Permissions']==1) {
    // User not logged in
    echo json_encode(
        array(
            "error" => "User has no permissions.",
            "code" => 209
        )
    );
    return;
}

header('Content-Type: application/json; charset=utf-8');

try{
    $employees = $db->getAllEmployees();

    // Order created successfully
    echo json_encode($employees);
}
catch(PDOException $e){
    // Processing errors
    echo json_encode(array(
        "error" => "Internal error processing order.",
        "code" => 305
    ));
    return;
}