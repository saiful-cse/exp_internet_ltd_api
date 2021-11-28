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
include_once  '../objects/admin.php';


// generate json web token
include_once '../config/core.php';
include_once '../libs/php-jwt-master/src/BeforeValidException.php';
include_once '../libs/php-jwt-master/src/ExpiredException.php';
include_once '../libs/php-jwt-master/src/SignatureInvalidException.php';
include_once '../libs/php-jwt-master/src/JWT.php';

use \Firebase\JWT\JWT;

/*
 * Instance database and dashboard object
 */
$database = new Database();
$db = $database->getConnection();
/*
* Initialize object
*/

$admin_id = $_POST['admin_id'];
$pin = $_POST['pin'];


if (!empty($admin_id && !empty($pin))) {

    $admin = new Admin($db);
    $admin->admin_id = $admin_id;
    $admin->pin = $pin;

    $stmt =  $admin->login();
    $num = $stmt->rowCount();

    if ($num > 0) {

        $token = array(
            "iat" => $issued_at,
            "exp" => $expiration_time,
            "iss" => $issuer,
            "data" => $admin_id
        );

        // generate jwt
        $jwt = JWT::encode($token, $key);
        echo json_encode(
            array(
                "admin_id" => $admin_id,
                "status" => 200,
                "message" => "Login Successfully",
                "jwt" => $jwt
            )
        );
    } else {

        echo json_encode(array(
            "status" => 404,
            "message" => "User not exist."
        ));
    }
} else {
    echo json_encode(array(
        "status" => 416,
        "message" => "Data Incomplete."
    ));
}
