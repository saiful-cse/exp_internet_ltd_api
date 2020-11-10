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

$id = $_GET['id'];


if (!empty($id))
{

    /*
 * Initialize object
 */
    $client = new Client($db);

    $client->id = $id;

    $stmt = $client->client_details();
    $num = $stmt->rowCount();

    if ($num>0)
    {
        //active client array
        $client_arr["client_details"] = array();

        $payment_arr["payment_details"] = array();

        //retrieve the table contents
        while($row = $stmt->fetch(PDO::FETCH_ASSOC))
        {
            
            array_push($client_arr["client_details"], $row);

        }
        echo json_encode($client_arr);

    }else{

        echo json_encode(array("message" => "No data found!!"));
    }

}else{

    echo json_encode(array("message" => "Data incomplete, Try again later!!"));
}




