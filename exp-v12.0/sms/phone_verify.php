<?php
date_default_timezone_set("Asia/Dhaka");
include_once '../config/url_config.php';
// required headers
header("Access-Control-Allow-Origin:" . $BASE_URL . $SECOND_PATH);
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

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

$number = $_POST['phone'];
$otp = $_POST['otp'];
/*
 * Instance database and dashboard object
 */
$database = new Database();
$db = $database->getConnection();

/*
 * Initialize object
 */
$sms = new Sms($db);

function sms_send($number, $otp)
{
    include '../config/url_config.php';
    $message = "WiFi connection registration OTP is " . $otp;
    $data = [
        "api_key" => $sms_api_key,
        "senderid" => $sms_api_senderid,
        "number" => $number,
        "message" => $message
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $sms_api_url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}

//data check
if (!empty($number) && !empty($otp)) {

    $sms_send_response = json_decode(sms_send($number, $otp), true);

    if ($sms_send_response['response_code'] == 202) {
        echo json_encode(array(
            "status" => 200,
            "message" => "SMS sent successfully"
        ));
    } else {
        echo json_encode(array(
            "status" => 201,
            "message" => "[" . $sms_send_response['response_code'] . "]" .
                ", " . $sms_send_response['error_message']
        ));
    }

} else {

    echo json_encode(array(
        "status" => 416,
        "message" => "Data Incomplete."
    ));
}
