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
include_once  '../objects/device.php';


// generate json web token
include_once '../config/core.php';
include_once '../libs/php-jwt-master/src/BeforeValidException.php';
include_once '../libs/php-jwt-master/src/ExpiredException.php';
include_once '../libs/php-jwt-master/src/SignatureInvalidException.php';
include_once '../libs/php-jwt-master/src/JWT.php';

use \Firebase\JWT\JWT;

$data = json_decode(file_get_contents("php://input"));

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
    require '../config/url_config.php';
    $message = "WiFi মেয়াদ শেষ, অটো চালু করতে লিংকে বিল পে করুন৷\nbaycombd.com/paybill/";

    
    $data = [
        "api_key" => $sms_api_key,
        "senderid" => $sms_api_senderid,
        "number" => $numbers,
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

if (!empty($data->jwt)) {

    try {

        // decode jwt
        $decoded = JWT::decode($data->jwt, $key, array('HS256'));

        $stmtppp = $sms->expiredClientsPPPname();
        $stmtphone = $sms->expiredClientsPhone();

        if ($stmtppp->rowCount() > 0 && $stmtphone->rowCount() > 0) {

            //Collecting phone numbers and ppp name
            while ($row = $stmtppp->fetch(PDO::FETCH_ASSOC)) {

                $pppName[] = $row['ppp_name'];
                $id[] = $row['id'];
            }
            while ($row = $stmtphone->fetch(PDO::FETCH_ASSOC)) {

                $num[] = $row['phone'];
            }
            $ids =  implode(', ', $id);
            $numbers =  implode(', ', $num);

            $device = new Device($db);
            $stmt = $device->get_device_url();

            //retrieve the table contents
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

                $url = $row['api_base'] . "pppListDisable.php";
                $login_ip = $row['login_ip'];
                $username = $row['username'];
                $password = $row['password'];
            }

            $postdata = array(
                'login_ip' => $login_ip,
                'username' => $username,
                'password' => $password,
                'ppp_names' => $pppName
            );

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postdata));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            $result = curl_exec($ch);
            curl_close($ch);

            //Disable and Remove form mikrotik server
            $mikrotik_response = json_decode($result, true);

            if ($mikrotik_response['status'] == 200) {

                $sms_send_response = json_decode(sms_send($numbers), true);

                $sms->ids = $ids;
                if ($sms_send_response['response_code'] == 202) {
                    if ($sms->clientDisconnectModeUpdate()) {
                        echo json_encode(array(
                            "status" => 200,
                            "message" => "SMS sent and disconnected successfully"
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
                    "status" => $mikrotik_response['status'],
                    "message" => $mikrotik_response['message']
                ));
            }
        } else if ($stmtppp->rowCount() > 0) {


            while ($row = $stmtppp->fetch(PDO::FETCH_ASSOC)) {

                $pppName[] = $row['ppp_name'];
                $id[] = $row['id'];
            }
            $ids =  implode(', ', $id);

            $device = new Device($db);
            $stmt = $device->get_device_url();

            //retrieve the table contents
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

                $url = $row['api_base'] . "pppListDisable.php";
                $login_ip = $row['login_ip'];
                $username = $row['username'];
                $password = $row['password'];
            }

            $postdata = array(
                'login_ip' => $login_ip,
                'username' => $username,
                'password' => $password,
                'ppp_names' => $pppName
            );

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postdata));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            $result = curl_exec($ch);
            curl_close($ch);

            //Disable and Remove form mikrotik server
            $mikrotik_response = json_decode($result, true);

            if ($mikrotik_response['status'] == 200) {

                $sms->ids = $ids;
                if ($sms->clientDisconnectModeUpdate()) {
                    echo json_encode(array(
                        "status" => 200,
                        "message" => "PPP disconnected successfully"
                    ));
                }

            } else {
                echo json_encode(array(
                    "status" => $mikrotik_response['status'],
                    "message" => $mikrotik_response['message']
                ));
            }
        } else {
            echo json_encode(array(
                "status" => 404,
                "message" => "Not found expired clients in this time."
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
