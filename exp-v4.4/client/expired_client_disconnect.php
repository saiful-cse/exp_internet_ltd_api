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

function pppListDisable($pppName)
{
    $url = 'http://mt.baycombd.com/expnet_api/pppListDisable.php';

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($pppName));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}


if (!empty($data->jwt)) {

    try {

        // decode jwt
        $decoded = JWT::decode($data->jwt, $key, array('HS256'));

        // decode jwt
        $sms->current_date = date("Y-m-d H:i:s");

        $stmt = $sms->getExpiredClientsPhonePPPname();

        if ($stmt->rowCount() > 0) {

            //Collecting phone numbers and ppp name
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

                $num[] = $row['phone'];
                $pppName[] = $row['ppp_name'];
                $id[] = $row['id'];
            }
            $numbers =  implode(', ', $num);
            $sms->id_list = implode(', ', $id);

            //Disable and Remove form mikrotik server
            $data = json_decode(pppListDisable($pppName), true);

            if ($data['status'] == 200) {

                //after success disabled, send sms
                $message = "আপনার WiFi সংযোগের মেয়াদ শেষ, পুনরায় চালু করতে বিল পরিশোধ করুন।\n https://expert-internet.net/paybill \n01975-559161 (bKash Payment)";

                $url = "http://66.45.237.70/api.php";
                $data = array(
                    'username' => "01835559161",
                    'password' => "saiful@#21490",
                    'number' => $numbers,
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

                        echo json_encode(array(
                            "status" => 1010,
                            "message" => "Max number limit exceeded"

                        ));
                        break;

                    case '1101':

                        //Update clients mode status on DB
                        if ($sms->clientDisconnectModeUpdate()) {
                            echo json_encode(array(

                                "status" => 200,
                                "message" => "SMS sent and disconnected successfully"

                            ));
                        } else {

                            echo json_encode(array(
                                "status" => 201,
                                "message" => "SMS sending error!!"
                            ));
                        }
                        break;
                }
            } else {
                echo json_encode(array(
                    "status" => $data['status'],
                    "message" => $data['message']
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
