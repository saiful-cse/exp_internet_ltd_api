<?php
date_default_timezone_set("Asia/Dhaka");
include_once '../config/url_config.php';
// required headers
header("Access-Control-Allow-Origin:" . $BASE_URL . $SECOND_PATH);
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

//RoterOS
use PEAR2\Net\RouterOS;

require_once '../PEAR2/Autoload.php';

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

if (!empty($data->jwt)) {

    try {
        // decode jwt
        $decoded = JWT::decode($data->jwt, $key, array('HS256'));

        try {
            $util = new RouterOS\Util(
                $client = new RouterOS\Client($login_ip, $username, $password)
            );

            $util->setMenu('/ppp/active');

            $client_array["ar"] = array();

            //not create another card file, adjusting same file

            foreach ($util->getAll() as $item) {

                if ($item->getProperty("comment") == "saiful") {
                    $ar = array(
                        "name" => $item->getProperty("name"),
                        "phone" => "Uptime: " . $item->getProperty("uptime"),
                        "ppp_name" => "MAC: " . $item->getProperty("caller-id"),
                        "area" => "router"
                    );
                    array_push($client_array["ar"], $ar);
                }
            }

            if (!empty($client_array["ar"])) {

                echo json_encode(
                    array(
                        "status" => 200,
                        "message" => "Online client fetched successfully",
                        "clients" => $client_array["ar"]
                    )
                );
            } else {
                echo json_encode(
                    array(
                        "status" => 404,
                        "message" => "Not found online client."
                    )
                );
            }
        } catch (\Throwable $th) {
            echo json_encode(
                array(
                    "status" => 500,
                    "message" => "Unable to connect mikrotik server."
                )
            );
        }
    } catch (\Throwable $e) {
        // tell the user access denied  & show error message
        echo json_encode(array(
            "status" => 401,
            "message" => "Access denied",
            "error" => $e->getMessage()
        ));
    }
} else {
    echo json_encode(array(
        "status" => 416,
        "message" => "Data Incomplete."
    ));
}
