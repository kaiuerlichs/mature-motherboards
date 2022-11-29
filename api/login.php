<?php
    require_once("./Database.php");

    $db = new Database();
    $db->connect();

    session_start();

    if(!isset($_SESSION["loggedIn"])){
        // Log user in
        if(isset($_POST["email"]) and isset($_POST["password"])){
            $email = $_POST["email"];
            $password = hash("sha256", $_POST["password"]);

            try{
                if($db->GetPasswordHash($email) == $password){
                    // Correct password
                    $_SESSION["email"] = $email;
                    $_SESSION["loggedIn"] = true;
                    // $_SESSION["userType"] = $db->GetEmployeeRole($email);

                    echo json_encode(array(
                        "message" => "Login successful",
                        "user" => $email,
                        "code" => 101
                    ));
                }
                else{
                    header('Content-Type: application/json; charset=utf-8');
                    // Incorrect password
                    echo json_encode(array(
                        "error" => "Password incorrect",
                        "code" => 104
                    ));
                }
            }
            catch(InvalidArgumentException $e) {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(array(
                    "error" => "No user with specified email",
                    "code"=> 103
                ));
                session_destroy();
            }
        }

        // Missing parameters
        else{
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(array(
                "error" => "Missing email or password",
                "code" => 102
            ));
        }
    }
    else{
        header('Content-Type: application/json; charset=utf-8');
        // User already logged in
        echo json_encode(array(
            "error" => "Already logged in",
            "code" => 100
        ));
    }