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

$database = new Database();
$db = $database->getConnection();

/*
 * Initialize object
 */
$txn = new Txn($db);

$jwt = $_POST['jwt'];
$txn_type = $_POST['type'];
$amount = $_POST['amount'];
$details = $_POST['details'];
$method = $_POST['method'];
$userid = $_POST['userid'];

if (
    !empty($jwt) && !empty($txn_type) && !empty($amount) && !empty($details) &&
    !empty($method) && !empty($userid)
) {

    try {
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));

        $txn->amount = $amount;
        $txn->details = $details;
        $txn->method = $method;
        $txn->userid = $userid;
        $txn->date = date("Y-m-d H:i:s");

        if ($txn->admin_txn($txn_type)) {
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
