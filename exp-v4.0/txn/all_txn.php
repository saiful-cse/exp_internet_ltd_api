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
$get_last_date = $_GET['last_date'];;


if (!empty($get_first_date) && !empty($get_last_date))
{

    /*
 * Initialize object
 */
    $txn = new Txn($db);

    $txn->first_date = $get_first_date;
    $txn->last_date = $get_last_date;


    $stmt = $txn->all_txn();
    $num = $stmt->rowCount();

    if ($num>0)
    {
        //active client array
        $txn_arr["all_txn"] = array();

        //retrieve the table contents
        while($row = $stmt->fetch(PDO::FETCH_ASSOC))
        {
            extract($row);

            $each_txn = array(
                "txn_id" => $txn_id,
                "client_id" => $client_id,
                "name" => $name,
                "date" => $date,
                "credit" => $credit,
                "debit" => $debit,
                "details" => $details
            );

            array_push($txn_arr["all_txn"], $each_txn);
        }
        echo json_encode($txn_arr);
    }else{

        echo json_encode(array("message" => "No transaction found!!"));
    }

}else{

    echo json_encode(array("message" => "Data incomplete, Try again later!!"));
}




