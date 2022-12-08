<?php
require_once(__DIR__ . "/../Database.php");

$db = new Database();
$db->connect();

$_POST = json_decode(file_get_contents('php://input'), true);

session_start();
header('Content-Type: application/json; charset=utf-8');

if (isset($_SESSION["loggedIn"])) {
    if ($_SESSION["permissions"] == 2) {
        if(isset($_POST["id"]) and isset($_POST["status"])){
            try{
                $db->UpdateRepairStatus($_POST["id"], $_POST["status"]);
            }
            catch(PDOException $e){
                error_log($e);
                // Processing errors
                echo json_encode(array(
                    "error" => "Internal error updating shifts.",
                    "code" => 4
                ));
                return;
            }
            // Missing keys
            echo json_encode(array(
                "message" => "Updated successfully.",
                "code" => 0
            ));
            return;
        }
        else{
            // Missing keys
            echo json_encode(array(
                "error" => "Missing keys.",
                "code" => 1
            ));
            return;
        }
    } else {
        // User no permissions
        echo json_encode(
            array(
                "error" => "User has no permissions.",
                "code" => 2
            )
        );
        return;
    }
} else {
    // User not logged in
    echo json_encode(
        array(
            "error" => "User not logged in.",
            "code" => 3
        )
    );
}