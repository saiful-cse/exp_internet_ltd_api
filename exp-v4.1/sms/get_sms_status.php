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
include_once  '../objects/sms.php';
include_once  '../objects/dashboard.php';
include_once  '../objects/client.php';


/*
 * Instance database and dashboard object
 */
    $database = new Database();
    $db = $database->getConnection();

    /*
     * Initialize object
     */
    $sms = new Sms($db);
    $dashboard = new Dashboard($db);
    $client = new Client($db);

    $client->current_date = date("Y-m-d H:i:s");
    $sms->current_date = date("Y-m-d H:i:s");

    $total_alert_client = $client->count_alert_client();
    $total_active_client = $dashboard->count_total_active_client();
    $total_sent = $sms->count_sms_sent();
    $total_not_sent = $sms->count_sms_unsent();

    $data = ['total_active' => $total_active_client,'total_alert' => $total_alert_client, "sent" => $total_sent , "unsent" => $total_not_sent];
    echo json_encode($data);


