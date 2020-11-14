<?php
// Requires headers
date_default_timezone_set("Asia/Dhaka");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

/*
 * include database and object files
 */
include_once '../config/database.php';
include_once  '../objects/client.php';

/*
 * Instance database and news object
 */
$database = new Database();
$db = $database->getConnection();

/*
 * Initialize object
 */
$client = new Client($db);

$name = $_POST['name'];
$phone = $_POST['phone'];
$email = $_POST['email'];
$address = $_POST['address'];
$location = $_POST['location'];

if (!empty($phone) && !empty($name) ){

    $client->name = $name;
    $client->phone = $phone;


    if ($client->registration())
    {
        //if success
        echo json_encode(array("message" => "ok"));

    }else{
        echo json_encode(array("message" => "Something went wrong"));
    }

}else{

    echo json_encode(array("message" => "Data incomplete, Try again later!!"));
}

