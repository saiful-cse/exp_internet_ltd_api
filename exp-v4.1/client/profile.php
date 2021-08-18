<?php

//Required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=UTF-8");

/*
 * include database and object files
 */
include_once '../config/database.php';
include_once  '../objects/client.php';

/*
 * Instance database and dashboard object
 */
$database = new Database();
$db = $database->getConnection();

/*
* Initialize object
*/
$client = new Client($db);

$phone = $_GET['phone'];
$client->phone = $phone;

$client->profile();

if ($client->id != null) {

    $profile_data = array(
        "mode" => $client->mode,
        "alert" => $client->alert,

        "id" => $client->id,
        "name" => $client->name,
        "phone" => $client->phone,
        "address" => $client->address,
        "email" => $client->email,

        "int_conn_type" => $client->int_conn_type,
        "username" => $client->username,
        "password" => $client->password,
        "onu_mac" => $client->onu_mac,

        "speed" => $client->speed,
        "fee" => $client->fee,
        "bill_type" => $client->bill_type,

        "reg_date" => $client->reg_date,
        "active_date" => $client->active_date,
        "inactive_date" => $client->inactive_date
    );

    // make it json format
    echo json_encode($profile_data);

}else{

    echo json_encode(array("message" => "Please!! Verify your phone number again."));
}




