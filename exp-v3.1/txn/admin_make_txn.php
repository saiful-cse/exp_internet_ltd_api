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

$txn_type = $_POST['type'];
$amount = $_POST['amount'];
$details = $_POST['details'];
$method = $_POST['method'];
$userid = $_POST['userid'];


if (!empty($txn_type) && !empty($amount) && !empty($details) && !empty($method) && !empty($userid)) {

    $txn->amount = $amount;
    $txn->details = $details;
    $txn->method = $method;
    $txn->userid = $userid;
    $txn->date = date("Y-m-d H:i:s");

    if ($txn->admin_txn($txn_type)) {
        //if success
        echo json_encode(array("message" => 201));
        
    } else {
        echo json_encode(array("message" => "Error!!"));
    }
} else {
    echo json_encode(array("message" => "Data incomplete, Try again later!!"));
}
