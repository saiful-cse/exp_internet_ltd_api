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
    !empty($data->jwt) &&  !empty($data->zone) && !empty($data->name) && !empty($data->phone) && !empty($data->area_id)

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

        $client->name = $data->name;
        $client->phone = $data->phone;
        $client->area_id = $data->area_id;
        $client->zone = $data->zone;

        if ($client->isExistPhone()) {
            echo json_encode(array(
                "status" => 207,
                "message" => "এই Phone নাম্বারটি দিয়ে একবার রেজিস্ট্রেশন হয়ে গেছে।"
            ));
            
        } else {

            $client->clientRegistration();
            echo json_encode(array(
                "status" => 200,
                "message" => "Client Registration Successfully."
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
