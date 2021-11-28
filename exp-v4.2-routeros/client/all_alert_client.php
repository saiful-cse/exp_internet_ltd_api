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

$total_alert_client = $client->count_alert_client();
$stmt = $client->all_alert_client();
$num = $stmt->rowCount();

if ($num > 0) {

    $client_arr["alert_client"] = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        $d[] = $row;
    }
    $data = ['total' => $total_alert_client, 'alert_client' => $d];
    echo json_encode($data);
} else {
    echo json_encode(array("message" => "No found alert client"));
}
