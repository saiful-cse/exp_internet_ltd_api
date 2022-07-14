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
    !empty($data->name) && !empty($data->phone) && !empty($data->area) && !empty($data->zone) && !empty($data->expire_date) &&
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
        $client->area = $data->area;
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

        } else if($client->client_details_update()){
            echo json_encode(array(
                "status" => 200,
                "message" => "Details Updated Successfully."
            ));
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
