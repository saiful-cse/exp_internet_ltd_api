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
include_once  '../objects/client.php';
include_once  '../objects/device.php';


// generate json web token
include_once '../config/core.php';
include_once '../libs/php-jwt-master/src/BeforeValidException.php';
include_once '../libs/php-jwt-master/src/ExpiredException.php';
include_once '../libs/php-jwt-master/src/SignatureInvalidException.php';
include_once '../libs/php-jwt-master/src/JWT.php';

use \Firebase\JWT\JWT;

$data = json_decode(file_get_contents("php://input"));

if (
    !empty($data->jwt) && !empty($data->id) && !empty($data->mode) && !empty($data->payment_method) &&
    !empty($data->name) && !empty($data->document) && !empty($data->phone)  && !empty($data->area_id) && !empty($data->zone) && !empty($data->expire_date) &&
    !empty($data->disable_date) && !empty($data->ppp_name) && !empty($data->ppp_pass) &&
    !empty($data->pkg_id)

) {

    try {
        // decode jwt
        $decoded = JWT::decode($data->jwt, $key, array('HS256'));

        //////////////////////////////////////
        //Fetching client information form DB
        //////////////////////////////////////
        $database = new Database();
        $db = $database->getConnection();
        $client = new Client($db);

        //Assing the value in client class
        $client->id = $data->id;
        $client->mode = $data->mode;
        $client->payment_method = $data->payment_method;
        $client->name = $data->name;
        $client->phone = $data->phone;
        $client->take_time = $data->take_time;
        $client->area_id = $data->area_id;
        $client->zone = $data->zone;
        $client->expire_date = $data->expire_date;
        $client->disable_date = $data->disable_date;
        $client->ppp_name = $data->ppp_name;
        $client->ppp_pass = $data->ppp_pass;
        $client->pkg_id = $data->pkg_id;

        if ($client->isExistPPPname()) {
            echo json_encode(array(
                "status" => 207,
                "message" => "আপনার দেওয়া PPP Name টি অন্য ক্লায়েন্টের জন্য ব্যবহার করা হয়েছে, আবার চেস্টা করুন।"
            ));
        } else if ($client->isExistPhoneToUpdate()) {
            echo json_encode(array(
                "status" => 207,
                "message" => "এই নাম্বারটি দিয়ে একবার রেজিস্ট্রেশন হয়ে গেছে।"
            ));
        } else {

            // ------------Mikrotik Connection Start ---------

            $device = new Device($db);
            $stmt = $device->get_device_url();

            //retrieve the table contents
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

                $url = $row['api_base'] . "pppUpdate.php";
                $login_ip = $row['login_ip'];
                $username = $row['username'];
                $password = $row['password'];
            }

            $postdata = array(
                'ppp_name' => $client->currentPppName(),
                'ppp_new_name' => $data->ppp_name,
                'ppp_pass' => $data->ppp_pass,
                'pkg_id' => $data->pkg_id,
                'mode' => $data->mode,

                'login_ip' => $login_ip,
                'username' => $username,
                'password' => $password
            );

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postdata));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            $result = curl_exec($ch);
            curl_close($ch);
            $mikrotik_response = json_decode($result, true);

            if ($mikrotik_response['status'] == 200) {

                if (file_exists('../documents/' . $data->document)) {
                    $client->document =  $data->document;
                    if ($client->client_details_update()) {
                        echo json_encode(array(
                            "status" => 200,
                            "message" => "Details Updated Successfully."
                        ));
                    }

                } else {

                    unlink('../documents/'.$client->current_document());
                    
                    $file_name = uniqid() . '.jpeg';
                    $client->document =  $file_name;

                    if ($client->client_details_update() && file_put_contents("../documents/" . $file_name, base64_decode($data->document))) {
                        echo json_encode(array(
                            "status" => 200,
                            "message" => "Details Updated Successfully."
                        ));
                    }
                }
            } else {
                echo json_encode(array(
                    "status" => $data['status'],
                    "message" => $data['message']
                ));
            }
        }
    } catch (\Throwable $e) {
        // tell the user access denied  & show error message
        echo json_encode(array(
            "status" => 401,
            "message" => "Access denied, error: " . $e->getMessage(),
            "error" => $e->getMessage()
        ));
    }
} else {
    echo json_encode(array(
        "status" => 416,
        "message" => "Data Incomplete."
    ));
}
