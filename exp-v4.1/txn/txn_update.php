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

//Instantiate database and user object
$database = new Database();
$db = $database->getConnection();

//Initialize object
$txn = new Txn($db);


// getting data from user
$jwt = $_POST['jwt'];
$txn_id = $_POST['txn_id'];
$date = $_POST['date'];
$details = $_POST['details'];
$credit = $_POST['credit'];
$debit = $_POST['debit'];

if (
    !empty($jwt) && !empty($txn_id) && !empty($date) && !empty($details)
    && !empty($credit) && !empty($debit)
) {

    $txn->txn_id = $txn_id;
    $txn->date = $date;
    $txn->details = $details;
    $txn->credit = $credit;
    $txn->debit = $debit;

    try {
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));

        if ($txn->txn_update()) {

            //if success
            echo json_encode(array(
                "status" => 200,
                "message" => "Transaction has been updated successfully"
            ));
        }
        // if unable to create the user, tell the user
        else {

            echo json_encode(array(
                "status" => 201,
                "message" => "Transaction not updated"
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
