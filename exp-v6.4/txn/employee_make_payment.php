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
include_once  '../objects/txn.php';


// generate json web token
include_once '../config/core.php';
include_once '../libs/php-jwt-master/src/BeforeValidException.php';
include_once '../libs/php-jwt-master/src/ExpiredException.php';
include_once '../libs/php-jwt-master/src/SignatureInvalidException.php';
include_once '../libs/php-jwt-master/src/JWT.php';

use \Firebase\JWT\JWT;


/*
 * Instance database and news object
 */

$data = json_decode(file_get_contents("php://input"));

if (
    !empty($data->jwt) && !empty($data->client_id) && !empty($data->name) && !empty($data->emp_id) && 
    !empty($data->txn_type) && !empty($data->method) &&
    !empty($data->details) && !empty($data->amount)
) {

    try {
        // decode jwt
        $decoded = JWT::decode($data->jwt, $key, array('HS256'));

        $database = new Database();
        $db = $database->getConnection();
        $txn = new Txn($db);

        $txn->client_id = $data->client_id;
        $txn->emp_id = $data->emp_id;
        $txn->name = $data->name;
        $txn->type = $data->txn_type;
        $txn->method = $data->method;
        $txn->details = $data->details;
        $txn->amount = $data->amount;
        $txn->date = date("Y-m-d H:i:s");

        if ($txn->employee_make_payment()) {
            //if success
            echo json_encode(array(
                "status" => 200,
                "message" => "Transaction has been successfully"
            ));

        } else {
            echo json_encode(array(
                "status" => 201,
                "message" => "Transaction not Success"
            ));
        }
    } catch (\Throwable $th) {
        // tell the user access denied  & show error message
        echo json_encode(array(
            "status" => 401,
            "message" => "Login time expired!!",
            "error" => $th->getMessage()
        ));
    }
} else {
    echo json_encode(array(
        "status" => 416,
        "message" => "Data Incomplete."
    ));
}
