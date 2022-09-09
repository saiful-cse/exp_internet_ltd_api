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
$number = $_POST['phone'];
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
if (!empty($jwt) && !empty($message) && !empty($number) && !empty($client_id)) {

    try {

        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));

        //set the value
        $sms->msg_body = $message;
        $sms->client_id = $client_id;
        $sms->created_at = date("Y-m-d H:i:s");

        //SMS service
        $url = "http://66.45.237.70/api.php";
        $data = array(
            'username' => "01835559161",
            'password' => "saiful@#21490",
            'number' => $number,
            'message' => $message
        );

        $ch = curl_init(); // Initialize cURL
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $smsresult = curl_exec($ch);

        $p = explode("|", $smsresult);
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

                echo json_encode(array(
                    "status" => 1006,
                    "message" => "Insufficient Balance"

                ));
                break;

            case '1009':
                echo json_encode(array(
                    "status" => 1009,
                    "message" => "Inactive Account, contact with software developer."

                ));
                break;
            case '1010':
                echo json_encode(array("message" => "Max number limit exceeded"));
                break;
            case '1101':

                if ($sms->idwise_sms_store()) {
                    echo json_encode(array(

                        "status" => 200,
                        "message" => "SMS sent successfully"

                    ));
                } else {

                    echo json_encode(array(
                        "status" => 201,
                        "message" => "SMS sending error!!"
                    ));
                }
                break;
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
