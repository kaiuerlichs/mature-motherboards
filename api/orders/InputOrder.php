
<?php
//email, productid, addr=instore
require_once("../Database.php");
require_once("../libraries/mailing/Mailer.php");

$db = new Database();
$db->connect();

header('Content-Type: application/json; charset=utf-8');

$_POST = json_decode(file_get_contents('php://input'), true);

$topLevelKeys = ["email", "productID", "charge"];


foreach($topLevelKeys as $key){
    if(!isset($_POST[$key])){
        // Missing keys
        echo json_encode(array(
            "error" => "Missing keys.",
            "code" => 301
        ));
        return;
    }

}



try{
    $orderNumber = $db->InputOrder($_POST["email"], $_POST["productID"], $_POST["charge"]);

    // Order created successfully
    echo json_encode(array(
        "message" => "Order input successfully.",
        "ordID" => $orderNumber,
        "code" => 300
    ));


}
catch(PDOException $e){
    // Processing errors
    error_log($e);
    echo json_encode(array(
        "error" => "Internal error processing order.",
        "code" => 305
    ));
    return;
}