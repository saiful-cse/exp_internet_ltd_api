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

if (
    !empty($data->jwt) && !empty($data->id) && !empty($data->payment_method) &&
    !empty($data->name) && !empty($data->phone) && !empty($data->area) &&
    !empty($data->ex_pppname) && !empty($data->ppp_name) && !empty($data->ppp_pass) &&
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
        $client->payment_method = $data->payment_method;
        $client->name = $data->name;
        $client->phone = $data->phone;
        $client->area = $data->area;
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

            try {

                // ------------- Updating mikrotik router--------
                $util = new RouterOS\Util(
                    $router_client = new RouterOS\Client($login_ip, $username, $password)
                );

                //Removing active list after //Updating secret list
                $util->setMenu('/ppp/active');
                $util->remove(RouterOS\Query::where('name', $data->ex_pppname));

                //Getting ex_ppp_name id for update
                $printRequest = new RouterOS\Request('/ppp/secret/print');
                $printRequest->setArgument('.proplist', '.id');
                $printRequest->setQuery(RouterOS\Query::where('name', $data->ex_pppname));
                $ex_ppp_id = $router_client->sendSync($printRequest)->getProperty('.id');

                $setRequest = new RouterOS\Request('/ppp/secret/set');
                $setRequest->setArgument('numbers', $ex_ppp_id);
                $setRequest->setArgument('name', $data->ppp_name);
                $setRequest->setArgument('password', $data->ppp_pass);
                $setRequest->setArgument('profile', $data->pkg_id);

                if ($router_client->sendSync($setRequest)) {
                    $client->client_details_update();
                    echo json_encode(array(
                        "status" => 200,
                        "message" => "Details Updated Successfully."
                    ));
                }
            } catch (\Throwable $th) {
                echo json_encode(
                    array(
                        "status" => 500,
                        "message" => "Unable to connect mikrotik server."
                    )
                );
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
