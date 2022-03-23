<?php
date_default_timezone_set("Asia/Dhaka");
include_once '../config/url_config.php';
// required headers
header("Access-Control-Allow-Origin:" . $BASE_URL . $SECOND_PATH);
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->phone && $data->otp)) {
  
    $message = "Your 'Expert Internet' app verification code is: ".$data->otp;
    //SMS service
    $url = "http://66.45.237.70/api.php";
    $data= array(
    'username'=>"01835559161",
    'password'=>"saiful@#21490",
    'number'=>$data->phone,
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
            echo json_encode(array(
                "status" => 1000,
                "message" => "Invalid user or Password"
            ));
            break;

        case '1002':
            echo json_encode(array(
                "status" => 1002,
                "message" => "Empty Number"
            ));
            break;

        case '1003':
            echo json_encode(array(
                "status" => 1003,
                "message" => "Invalid message or empty message"
            ));
            break;

        case '1004':
            echo json_encode(array(
                "status" => 1004,
                "message" => "Invalid number"
            ));
            break;

        case '1005':
            echo json_encode(array(
                "status" => 1005,
                "message" => "All Number is Invalid"
            ));
            break;

        case '1006':
            echo json_encode(array(
                "status" => 1006,
                "message" => "Insufficient Balance"
            ));
            break;

        case '1009':
            echo json_encode(array(
                "status" => 1009,
                "message" => "Inactive Account"
            ));
            break;

        case '1010':
            echo json_encode(array(
                "status" => 1010,
                "message" => "Max number limit exceeded"
            ));
            break;

        case '1101':
            echo json_encode(array(
                "status" => 1101,
                "message" => "SMS sent successfylly"
            ));
            break;
    }

    
} else {
    echo json_encode(array(
        "status" => 416,
        "message" => "Data Incomplete."
    ));
}
