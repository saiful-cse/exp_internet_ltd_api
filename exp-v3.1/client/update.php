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
$mode = $_POST['mode'];
$name = $_POST['name'];
$phone = $_POST['phone'];
$address = $_POST['address'];
$email = $_POST['email'];
$area = $_POST['area'];

$int_conn_type = $_POST['int_conn_type'];
$username = $_POST['username'];
$password = $_POST['password'];
$onu_mac = $_POST['onu_mac'];

$speed = $_POST['speed'];
$fee = $_POST['fee'];
$bill_type = $_POST['bill_type'];

if (!empty($name) && !empty($mode) && !empty($id) && !empty($phone)&&
    !empty($address) && !empty($email) && !empty($area) && !empty($int_conn_type) &&
    !empty($username) && !empty($password) &&
    !empty($speed) && !empty($fee) && !empty($bill_type)){

    //getting current timestamp
    $current_date = date("Y-m-d H:i:s");

    $client->id = $id;
    $client->mode = $mode;
    $client->name = $name;
    $client->phone = $phone;
    $client->address = $address;
    $client->email = $email;
    $client->area = $area;

    $client->int_conn_type = $int_conn_type;
    $client->username = $username;
    $client->password = $password;
    $client->onu_mac = $onu_mac;
    
    $client->speed = $speed;
    $client->fee = $fee;
    $client->bill_type = $bill_type;
    $client->active_date = $current_date;
    $client->inactive_date = $current_date;

    if ($client->data_update())
    {
        //if success
        echo json_encode(array("message" => 200));

    }else{
        echo json_encode(array("message" => "Error!!"));
    }

}else{

    echo json_encode(array("message" => "Data incomplete, Try again later!!"));
}

