<?php
date_default_timezone_set("Asia/Dhaka");
include_once '../config/url_config.php';
// required headers
header("Access-Control-Allow-Origin:" . $BASE_URL . $SECOND_PATH);
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/database.php';
include_once  '../objects/note.php';

// generate json web token
include_once '../config/core.php';
include_once '../libs/php-jwt-master/src/BeforeValidException.php';
include_once '../libs/php-jwt-master/src/ExpiredException.php';
include_once '../libs/php-jwt-master/src/SignatureInvalidException.php';
include_once '../libs/php-jwt-master/src/JWT.php';

use \Firebase\JWT\JWT;

$database = new Database();
$db = $database->getConnection();
$noteOb = new Note($db);

if (!empty($_POST['jwt']) && !empty($_POST['note'])) {

    try {
        // decode jwt
        $decoded = JWT::decode($_POST['jwt'], $key, array('HS256'));

        $noteOb->note = $_POST['note'];
        $noteOb->updated_at = date("Y-m-d H:i:s");
        if($noteOb->updateNote()){
            echo json_encode(
                array(
                    "status" => 200,
                    "message" => "Note updated successfuly"
                )
            );
        }
        
    } catch (\Throwable $e) {
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
