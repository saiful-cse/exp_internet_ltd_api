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

$jwt = $_POST['jwt'];
$message = $_POST['message'];
$area = $_POST['area'];

/*
* Instance database and dashboard object
*/
$database = new Database();
$db = $database->getConnection();

/*
* Initialize object
*/
$sms = new Sms($db);

function sms_send($numbers)
{
    include '../config/url_config.php';
    $data = [
        "api_key" => $sms_api_key,
        "senderid" => $sms_api_senderid,
        "number" => $numbers,
        "message" => $_POST['message']
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

if (!empty($jwt) && !empty($message) && !empty($area)) {

    try {

        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));

        $sms->area = $area;
        $sms->msg_body = $message;
        $stmt = $sms->getting_areawise_client_phone();
        $data = $stmt->rowCount();

        if ($data > 0) {

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

                $num[] = $row['phone'];
            }
            $numbers =  implode(', ', $num);

            $sms_send_response = json_decode(sms_send($numbers), true);

            if ($sms_send_response['response_code'] == 202) {
                
                if($sms->areawise_sms_store()){
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

        } else {
            echo json_encode(array(
                "status" => 404,
                "message" => "Nothing areawise client to send SMS"
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
