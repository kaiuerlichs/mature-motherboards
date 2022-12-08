<?php

session_start();

if ($_SESSION["loggedIn"]) {
    $redirect = "";

    if ($role == 1)
        $redirect = "./manager_view.html";
    elseif ($role == 2)
        $redirect = "./repair_view.html";
    elseif ($role == 3)
        $redirect = "./sales_view.html";

    echo json_encode(
        array(
            "message" => "Redirection details",
            "user" => $email,
            "redirectUrl" => $redirect,
            "code" => 900
        )
    );
}
else{
    echo json_encode(
        array(
            "error" => "User not logged in",
            "code" => 200
        )
    );
}