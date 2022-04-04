<?php
session_start();

include_once '../config/database.php';
include_once  '../objects/txn.php';

$database = new Database();
$db = $database->getConnection();

//$client_id, $month, $name, $amount, $txnid;

$txn = new Txn($db);

$txn->client_id = $_SESSION['client_id'];
$txn->name = $_SESSION['name'];
$txn->amount = $_SESSION['amount'];
$txn->month = $_SESSION['month'];
$txn->details = $_SESSION['name'].", Bill, bKash, ".$_POST['txnid'].", ".$_POST['customerMsisdn'];


if ($txn->bkash_txn_store()) {
    echo json_encode(array(
        "status" => 200,
        "message" => "Txn success"
    ));
    
} else {
    echo json_encode(array(
        "status" => 400,
        "message" => "Txn error"
    ));
}
