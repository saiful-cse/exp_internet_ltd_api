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

$id = $_POST['id'];
$name = $_POST['name'];
$address = $_POST['address'];
$email = $_POST['email'];

if (!empty($id) && !empty($name) && !empty($address) && !empty($email)){


    $client->id = $id;
    $client->name = $name;
    $client->address = $address;
    $client->email = $email;

    if ($client->profile_update())
    {
        //if success
        echo json_encode(array("message" => "Your profile has been updated."));

    }else{
        echo json_encode(array("message" => "Error!!"));
    }

}else{

    echo json_encode(array("message" => "Data incomplete, Try again later!!"));
}

