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

if (!empty($data->jwt) and !empty($data->id)) {

    try {
        // decode jwt
        $decoded = JWT::decode($data->jwt, $key, array('HS256'));

        
        ////////////////////////////////////
        //Fetching client information form DB
        ////////////////////////////////////
        $database = new Database();
        $db = $database->getConnection();

        //set the ppp name in query
        $client = new Client($db);
        $client->id = $data->id;

        $stmt = $client->client_details_id();
        
        if($stmt->rowCount() > 0)
        {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $status = 200;
                $message = "Client details fetched successfully";
                $id = $row['id'];
                $mode = $row['mode'];

                $name = $row['name'];
                $document = $row['document'];
                $phone = $row['phone'];
                $take_time = $row['take_time'];
                $area_id = $row['area_id'];
                $area = $row['area'];
                $zone = $row['zone'];

                $reg_date = $row['reg_date'];
                $expire_date = $row['expire_date'];
                $disable_date = $row['disable_date'];
                $ppp_name = $row['ppp_name'];
                $ppp_pass = $row['ppp_pass'];
               
                $payment_method = $row['payment_method'];
                $pkg_id = $row['pkg_id'];
                $onu_mac = $row['onu_mac'];
                
            }
        }
        else{

            $message = "Not found client details via id";
            $status = 404;

        }
        ///////////////// END //////////////////

        //Printing data
        echo json_encode(
            array(
                "status" => $status,
                "message" => $message,
                "payment_method" => $payment_method,

                "id" => $id,
                "mode" => $mode,
                "name" => $name,
                "document" => $document,
                "phone" => $phone,
                "take_time" => $take_time,
                "area_id" => $area_id,
                "area" => $area,
                "zone" => $zone,
                "reg_date" => $reg_date,
                "expire_date" => $expire_date,
                "disable_date" => $disable_date,
                "ppp_name" => $ppp_name,
                "ppp_pass" => $ppp_pass,
                "pkg_id" => $pkg_id,
                "onu_mac" => $onu_mac
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
