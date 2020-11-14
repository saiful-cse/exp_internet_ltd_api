<?php
//Requires headers
date_default_timezone_set("Asia/Dhaka");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 5");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

/*
 * include database and object files
 */
include_once '../config/database.php';
include_once  '../objects/txn.php';

/*
 * Instance database and news object
 */
$database = new Database();
$db = $database->getConnection();

/*
 * Initialize object
 */
$txn = new Txn($db);

$id = $_POST['id'];
$name = $_POST['name'];
$amount = $_POST['amount'];
$details = $_POST['details'];
$payment_type = $_POST['type'];
$method = $_POST['method'];
$userid = $_POST['userid'];

if (!empty($id) && !empty($name) && !empty($amount) && !empty($details) && !empty($method) && !empty($userid))
{
    $txn->id = $id;
    $txn->name = $name;
    $txn->amount = $amount;
    $txn->details = $details;
    $txn->payment_type = $payment_type;
    $txn->method = $method;
    $txn->userid = $userid;
    $txn->date = date("Y-m-d H:i:s");

    if ($txn->client_txn())
    {
        //if success
        echo json_encode(array("message" => 201));

    }else{
        echo json_encode(array("message" => "Error!!"));
    }

}else
{
    echo json_encode(array("message" => "Data incomplete, Try again later!!"));
}

