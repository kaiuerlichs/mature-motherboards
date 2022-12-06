<?php
require_once(__DIR__ . "/../Database.php");

$db = new Database();
$db->connect();

session_start();

header('Content-Type: application/json; charset=utf-8');

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
if ($_SESSION['Permissions']!=2) {
    // User not logged in
    echo json_encode(
        array(
            "error" => "User has no permissions.",
            "code" => 209
        )
    );
    return;
}

try{
    $repairs = $db->getProductByID($_GET["branchid"]);

    // Order created successfully
    echo json_encode($repairs);
}
catch(PDOException $e){
    // Processing errors
    echo json_encode(array(
        "error" => "Internal error processing order.",
        "code" => 305
    ));
    return;
}