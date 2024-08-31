<?php
session_start();

include_once '../config/database.php';
include_once  '../objects/txn.php';

$database = new Database();
$db = $database->getConnection();

$txn = new Txn($db);

$_SESSION['trxID'] = $_POST['trxID'];
$_SESSION['customerMsisdn'] = $_POST['customerMsisdn'];
$_SESSION['completedTime'] = $_POST['completedTime'];

$txn->client_id = $_SESSION['client_id'];
$txn->name = $_SESSION['name'];
$txn->zone = $_SESSION['zone'];
$txn->amount = $_SESSION['amount'];
$txn->expire_date = $_SESSION['expire_date'];
$txn->details = $_SESSION['name'].", ".$_SESSION['ppp_name'].", Bill, bKash, ".$_SESSION['trxID'].", ".$_SESSION['customerMsisdn'];


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
