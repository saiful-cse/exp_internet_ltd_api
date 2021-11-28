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

//RoterOS
use PEAR2\Net\RouterOS;
require_once '../PEAR2/Autoload.php';

// generate json web token
include_once '../config/core.php';
include_once '../libs/php-jwt-master/src/BeforeValidException.php';
include_once '../libs/php-jwt-master/src/ExpiredException.php';
include_once '../libs/php-jwt-master/src/SignatureInvalidException.php';
include_once '../libs/php-jwt-master/src/JWT.php';

use \Firebase\JWT\JWT;

$data = json_decode(file_get_contents("php://input"));

if ($data->jwt && $data->ppp_name && $data->action && $data->id) {

    try {
        // decode jwt
        $decoded = JWT::decode($data->jwt, $key, array('HS256'));

        if ($data->action == "enable") {

            try {
                $util = new RouterOS\Util(
                    $client = new RouterOS\Client($login_ip, $username, $password)
                );

                $util->setMenu('/ppp/secret');

                $database = new Database();
                $db = $database->getConnection();
                $client = new Client($db);
                $client->id = $data->id;
                $client->mode = $data->action;

                if ($util->enable(RouterOS\Query::where('name', $data->ppp_name))) {
                    $client->modeUpdate();
                    echo json_encode(array(
                        "status" => 200,
                        "message" => "PPP enabled successfully"
                    ));
                } else {
                    echo json_encode(
                        array(
                            "status" => 404,
                            "message" => "PPP enable error!!"
                        )
                    );
                }
            } catch (\Throwable $th) {
                echo json_encode(
                    array(
                        "status" => 500,
                        "message" => "Unable to connect mikrotik server.",
                    )
                );
            }
        } else if ($data->action == "disable") {

            try {
                $util = new RouterOS\Util(
                    $client = new RouterOS\Client($login_ip, $username, $password)
                );

                $util2 = new RouterOS\Util(
                    $client = new RouterOS\Client($login_ip, $username, $password)
                );

                $util2->setMenu('/ppp/active');
                $util->setMenu('/ppp/secret');

                $database = new Database();
                $db = $database->getConnection();
                $client = new Client($db);
                $client->id = $data->id;
                $client->mode = $data->action;

                if (
                    $util2->remove(RouterOS\Query::where('name', $data->ppp_name)) &&
                    $util->disable(RouterOS\Query::where('name', $data->ppp_name))
                ) {
                    $client->modeUpdate();
                    echo json_encode(array(
                        "status" => 200,
                        "message" => "PPP disabled successfully"
                    ));
                    
                } else {
                    echo json_encode(
                        array(
                            "status" => 404,
                            "message" => "PPP disabled error!!"
                        )
                    );
                }
            } catch (\Throwable $th) {
                //throw $th;
            }
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
