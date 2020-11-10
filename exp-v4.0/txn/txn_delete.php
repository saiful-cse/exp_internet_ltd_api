<?php

// Requires headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

//Include database and object files
include_once '../config/database.php';
include_once '../objects/txn.php';

//Instantiate database and user object
$database = new Database();
$db = $database->getConnection();

//Initialize object
$txn = new Txn($db);


// getting data from user
$txn_id = $_GET['txn_id'];


if (!empty($txn_id)){

    $txn->txn_id = $txn_id;

    if ($txn->txn_delete()){

        // tell the user
        echo json_encode(array("message" => "Transaction has been deleted"));
    }
    // if unable to create the user, tell the user
    else{

        // tell the user
        echo json_encode(array("message" => "Something went wrong!! Try agian"));

    }

}else{
    
    // tell the user
    echo json_encode(array("message" => "Unable to publish post. Data is incomplete."));

}