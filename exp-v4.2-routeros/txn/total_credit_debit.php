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
include_once  '../objects/txn.php';

/*
 * Instance database and dashboard object
 */
$database = new Database();
$db = $database->getConnection();

$get_first_date = $_GET['first_date'];
$get_last_date = $_GET['last_date'];

/*
 * Initialize object
 */
$txn = new Txn($db);

$txn->first_date = $get_first_date;
$txn->last_date = $get_last_date;

//Getting counted value form database
$total_credit = $txn->datewise_total_credit();
$total_debit = $txn->datewise_total_debit();
$datewise_userwise_total = $txn->datewise_and_adminwise_total_credit_debit();
$num = $datewise_userwise_total->rowCount();

if ($num>0)
    {
        //retrieve the table contents
        while($row = $datewise_userwise_total->fetch(PDO::FETCH_ASSOC))
        {
            $d[] = $row;
        }
        //echo json_encode($txn_arr);
        $data = ['total_credit' => $total_credit, 'total_debit' => $total_debit, 'admin_cash' => $d];
        echo json_encode($data);

    }else{

        echo json_encode(array("message" => "No transaction found!!"));
    }

//JSON encode
// echo json_encode(
//     array(
//         "total_credit" => $total_credit,
//         "total_debit" => $total_debit

//     ));




