<?php
require_once("./Database.php");

$db = new Database();
$db->connect();

$_POST = json_decode(file_get_contents('php://input'), true);

session_start();
header('Content-Type: application/json; charset=utf-8');

if (isset($_SESSION["loggedIn"])) {
    // User logout
    session_destroy();

    echo json_encode(
        array(
            "error" => "Logged out successfuly",
            "code" => 201
        )
    );
} else {
    // User not logged in
    echo json_encode(
        array(
            "error" => "User not logged in",
            "code" => 200
        )
    );
}