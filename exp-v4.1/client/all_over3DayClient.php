<?php

//Required headers
date_default_timezone_set("Asia/Dhaka");
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

//Set the value on client class
$client->current_date = date("Y-m-d H:i:s");

$stmt = $client->all_over3Day_client();
$num = $stmt->rowCount();

if ($num > 0) {
    //active client array
    $client_arr["over3Day_client"] = array();

    //retrieve the table contents
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        array_push($client_arr["over3Day_client"], $row);
    }
    echo json_encode($client_arr);
} else {
    echo json_encode(array("message" => "No found over 3 Day client"));
}
