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

if (!empty($data->jwt) and !empty($data->ppp_name)) {

    try {
        // decode jwt
        $decoded = JWT::decode($data->jwt, $key, array('HS256'));

        //Default value set
        $status = $message = $id = $registered = $name = $phone = $take_time = $area = $zone =
                $ppp_name = $ppp_pass = $ppp_activity = $caller_id = $last_loged_out = $uptime = $mode = 
                $payment_method = $pkg_id = $reg_date = $expire_date = $disable_date = "---";

        ////////////////////////////////////
        //Fetching client information form DB
        ////////////////////////////////////
        $database = new Database();
        $db = $database->getConnection();

        //set the ppp name in query
        $client = new Client($db);
        $client->ppp_name = $data->ppp_name;

        $stmt = $client->client_details();
        $stmt2 = $client->all_packages();

        if($stmt->rowCount() > 0)
        {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $status = 200;
                $message = "Client details fetched successfully";
                $id = $row['id'];
                $registered = $row['registered'];
                $name = $row['name'];
                $phone = $row['phone'];
                $area = $row['area'];
                $zone = $row['zone'];
                $ppp_name = $row['ppp_name'];
                $ppp_pass = $row['ppp_pass'];
                $mode = $row['mode'];
                $payment_method = $row['payment_method'];
                $pkg_id = $row['pkg_id'];
                $reg_date = $row['reg_date'];
                $expire_date = $row['expire_date'];
                $disable_date = $row['disable_date'];
                $take_time = $row['take_time'];
            }
        }
        else{

            $message = "Not found client";
            $status = 404;
        }

        
        //Printing data
        echo json_encode(
            array(
                "status" => $status,
                "message" => $message,

                "id" => $id,
                "registered" => $registered,
                "name" => $name,
                "phone" => $phone,
                "area" => $area,
                "zone" => $zone,

                "ppp_name" => $ppp_name,
                "ppp_pass" => $ppp_pass,
 
                "mode" => $mode,
                "payment_method" => $payment_method,
                "pkg_id" => $pkg_id,
                "reg_date" => $reg_date,
                "expire_date" => $expire_date,
                "disable_date" => $disable_date,
                "take_time" => $take_time
            )
        );
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
