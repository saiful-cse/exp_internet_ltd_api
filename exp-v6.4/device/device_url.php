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

include_once '../config/core.php';
include_once '../libs/php-jwt-master/src/BeforeValidException.php';
include_once '../libs/php-jwt-master/src/ExpiredException.php';
include_once '../libs/php-jwt-master/src/SignatureInvalidException.php';
include_once '../libs/php-jwt-master/src/JWT.php';

use \Firebase\JWT\JWT;

include_once '../config/database.php';
include_once  '../objects/device.php';

if (!empty($_POST['jwt'])) {

    try {
        // decode jwt
        $decoded = JWT::decode($_POST['jwt'], $key, array('HS256'));
        $database = new Database();
        $db = $database->getConnection();
        $device = new Device($db);

        $stmt = $device->get_device_url();

        //retrieve the table contents
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo json_encode(
                array(
                    "id" => $row['id'],
                    "api_base" => $row['api_base'],
                    "login_ip" => $row['login_ip'],
                    "username" => $row['username'],
                    "password" => $row['password']
                )
            );
        }
    } catch (\Throwable $th) {
        // tell the user access denied  & show error message
        echo json_encode(array(
            "status" => 401,
            "message" => "Access denied",

        ));
    }
} else {
    echo json_encode(array(
        "status" => 416,
        "message" => "Data Incomplete."
    ));
}
