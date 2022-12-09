<?php

session_start();

if ($_SESSION["loggedIn"]) {
    $redirect = "";

    if ($_SESSION["permissions"] == 1)
        $redirect = "./manager_view.html";
    elseif ($_SESSION["permissions"] == 2)
        $redirect = "./repair_view.html";
    elseif ($_SESSION["permissions"] == 3)
        $redirect = "./sales_view.html";

        echo json_encode(
            array(
                "redirectUrl" => $redirect,
                "code" => 201
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