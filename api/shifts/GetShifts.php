<?php
require_once(__DIR__ . "/../Database.php");

$db = new Database();
$db->connect();


session_start();

header('Content-Type: application/json; charset=utf-8');


try{
    if(!isset($_SESSION["loggedIn"])){
        // User not logged in
        echo json_encode(
            array(
                "error" => "User not logged in",
                "code" => 200
            )
        );
    }
    $shift = $db->GetShiftsByID($_SESSION["id"]);

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