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

$txn_id = $_GET['txn_id'];


if (!empty($txn_id))
{

    /*
 * Initialize object
 */
    $txn = new Txn($db);

    $txn->txn_id = $txn_id;

    $stmt = $txn->txn_details();
    $num = $stmt->rowCount();

    if ($num>0)
    {
        //active client array
        $txn_arr["txn_details"] = array();

        //retrieve the table contents
        while($row = $stmt->fetch(PDO::FETCH_ASSOC))
        {
            extract($row);

            $each_txn = array(
                "client_id" => $client_id,
                "name" => $name,
                "type" => $type,
                "date" => $date,
                "emp_id" =>$emp_id,
                "details" => $details,
                "credit" => $credit,
                "debit" => $debit
            );

            array_push($txn_arr["txn_details"], $each_txn);

        }
        echo json_encode($txn_arr);


    }else{

        echo json_encode(array("message" => "No yet create transaction by this txn ID"));
    }

}else{

    echo json_encode(array("message" => "Data incomplete, Try again later!!"));
}




