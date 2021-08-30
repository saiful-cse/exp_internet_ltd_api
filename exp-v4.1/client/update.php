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

/*
 * Instance database and news object
 */
$database = new Database();
$db = $database->getConnection();

/*
 * Initialize object
 */
$client = new Client($db);

$jwt = $_POST['jwt'];
$id = $_POST['id'];
$mode = $_POST['mode'];
$name = $_POST['name'];
$phone = $_POST['phone'];
$address = $_POST['address'];
$email = $_POST['email'];
$area = $_POST['area'];

$username = $_POST['username'];
$password = $_POST['password'];

$speed = $_POST['speed'];
$fee = $_POST['fee'];
$payment_method = $_POST['payment_method'];

if (
    !empty($id) && !empty($jwt) && !empty($name) && !empty($mode) 
    && !empty($phone) && !empty($address) && !empty($email) && !empty($area) &&
    !empty($username) && !empty($password) &&
    !empty($speed) && !empty($fee) && !empty($payment_method)
) {


    try {
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));

        //getting current timestamp
        $current_date = date("Y-m-d H:i:s");

        $client->id = $id;
        $client->mode = $mode;
        $client->name = $name;
        $client->phone = $phone;
        $client->address = $address;
        $client->email = $email;
        $client->area = $area;

        $client->username = $username;
        $client->password = $password;
        
        $client->speed = $speed;
        $client->fee = $fee;
        $client->payment_method = $payment_method;

        $client->active_date = $current_date;
        $client->inactive_date = $current_date;

        if ($client->data_update()) {
            //if success
            echo json_encode(array(
                "status" => 200,
                "message" => "Updated Successfully"
            ));

        } else {
            echo json_encode(array(
                "status" => 201,
                "message" => "Not Updated"
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
