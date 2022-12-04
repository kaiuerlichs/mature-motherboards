<?php
require_once(__DIR__ . "./Database.php");

$db = new Database();
$db->connect();

$_POST = json_decode(file_get_contents('php://input'), true);

session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION["loggedIn"])) {
    // Log user in
    if (isset($_POST["email"]) and isset($_POST["password"])) {
        $email = $_POST["email"];
        $password = hash("sha256", $_POST["password"]);

        try {
            if ($db->GetPasswordHash($email) == $password) {
                // Correct password
                $_SESSION["email"] = $email;
                $_SESSION["loggedIn"] = true;

                echo json_encode(
                    array(
                        "message" => "Login successful",
                        "user" => $email,
                        "code" => 101
                    )
                );
            } else {
                // Incorrect password
                echo json_encode(
                    array(
                        "error" => "Password incorrect",
                        "code" => 104
                    )
                );
            }
        } catch (InvalidArgumentException $e) {
            echo json_encode(
                array(
                    "error" => "No user with specified email",
                    "code" => 103
                )
            );
            session_destroy();
        }
    }

    // Missing parameters
    else {
        echo json_encode(
            array(
                "error" => "Missing email or password",
                "code" => 102
            )
        );
    }
} else {
    // User already logged in
    echo json_encode(
        array(
            "error" => "Already logged in",
            "code" => 100
        )
    );
}