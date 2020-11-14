<?php

//Requires headers
date_default_timezone_set("Asia/Dhaka");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 5");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$message = $_POST['message'];
$number = $_POST['phone'];
$client_id = $_POST['client_id'];

/*
 * include database and object files
 */
include_once '../config/database.php';
include_once  '../objects/sms.php';


/*
 * Instance database and dashboard object
 */
$database = new Database();
$db = $database->getConnection();

/*
 * Initialize object
 */
$sms = new Sms($db);

//data check
if(!empty($message) && !empty($number) && !empty($client_id)){

    //set the value
    $sms->msg_body = $message;
    $sms->client_id = $client_id;
    $sms->created_at = date("Y-m-d H:i:s");

    //SMS service
    $url = "http://66.45.237.70/api.php";
    $data= array(
    'username'=>"01835559161",
    'password'=>"saiful@#21490",
    'number'=>$number,
    'message'=>$message
    );

    $ch = curl_init(); // Initialize cURL
    curl_setopt($ch, CURLOPT_URL,$url);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $smsresult = curl_exec($ch);

    $p = explode("|",$smsresult);
    $sendstatus = $p[0];

    switch ($sendstatus) {
        case '1000':
            echo json_encode(array("message" => "Invalid user or Password"));
            break;
        case '1002':
            echo json_encode(array("message" => "Empty Number"));
            break;
        case '1003':
            echo json_encode(array("message" => "Invalid message or empty message"));
            break;
        case '1004':
            echo json_encode(array("message" => "Invalid number"));
            break;
        case '1005':
            echo json_encode(array("message" => "All Number is Invalid"));
            break;
        case '1006':
            echo json_encode(array("message" => "Insufficient Balance"));
            break;
        case '1009':
            echo json_encode(array("message" => "Inactive Account"));
            break;
        case '1010':
            echo json_encode(array("message" => "Max number limit exceeded"));
            break;
        case '1101':
            
            if($sms->idwise_sms_store()){
                echo json_encode(array("message" => 200));
            }
            break;
    }

}else{

    echo json_encode(array("message" => "Data incomplete, Try again later!!"));
}
