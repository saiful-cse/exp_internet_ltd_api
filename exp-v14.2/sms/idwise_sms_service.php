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
include_once  '../sms_gateway/sms_send.php';


// generate json web token
include_once '../config/core.php';
include_once '../libs/php-jwt-master/src/BeforeValidException.php';
include_once '../libs/php-jwt-master/src/ExpiredException.php';
include_once '../libs/php-jwt-master/src/SignatureInvalidException.php';
include_once '../libs/php-jwt-master/src/JWT.php';

use \Firebase\JWT\JWT;

/*
 * Instance database and dashboard object
 */

$database = new Database();
$db = $database->getConnection();

$jwt = $_POST['jwt'];
$message = $_POST['message'];
$numbers = "88".$_POST['phone'];
$client_id = $_POST['client_id'];

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
if (!empty($jwt) && !empty($message) && !empty($numbers) && !empty($client_id)) {

    try {

        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));

        //set the value
        $sms->msg_body = $message;
        $sms->client_id = $client_id;
        $sms->created_at = date("Y-m-d H:i:s");

        $sms_send_response = json_decode(bulkbd_sms_send($numbers, $message), true);

        if ($sms_send_response['response_code'] == 202) {
            if ($sms->idwise_sms_store()) {
                echo json_encode(array(
                    "status" => 200,
                    "message" => "SMS sent successfully"
                ));
            }
        } else {
            echo json_encode(array(
                "status" => 201,
                "message" => "[" . $sms_send_response['response_code'] . "]" .
                    ", " . $sms_send_response['error_message']
            ));
        }
        
    } catch (\Throwable $th) {
        echo json_encode(array(
            "status" => 401,
            "message" => "Access denied",
            "error" => $th->getMessage()
        ));
    }
} else {

    echo json_encode(array(
        "status" => 416,
        "message" => "Data Incomplete."
    ));
}
