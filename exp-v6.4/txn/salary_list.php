<?php
date_default_timezone_set("Asia/Dhaka");
include_once '../config/url_config.php';
// required headers
header("Access-Control-Allow-Origin:" . $BASE_URL . $SECOND_PATH);
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


// generate json web token
include_once '../config/core.php';
include_once '../libs/php-jwt-master/src/BeforeValidException.php';
include_once '../libs/php-jwt-master/src/ExpiredException.php';
include_once '../libs/php-jwt-master/src/SignatureInvalidException.php';
include_once '../libs/php-jwt-master/src/JWT.php';

use \Firebase\JWT\JWT;

$data = json_decode(file_get_contents("php://input"));

/*
 * include database and object files
 */
include_once '../config/database.php';
include_once  '../objects/txn.php';

$database = new Database();
$db = $database->getConnection();


if (!empty($data->jwt) && !empty($data->employee_id)) {

    try {
        // decode jwt
        $decoded = JWT::decode($data->jwt, $key, array('HS256'));
        $txn = new Txn($db);

        $txn->employee_id = $data->employee_id;

        $stmt = $txn->salay_list();
        $num = $stmt->rowCount();

        if ($num > 0) {

            $salary_arr["ar"] = array();

            //retrieve the table contents
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                array_push($salary_arr["ar"], $row);
            }
            echo json_encode(
                array(
                    "status" => 200,
                    "message" => "Salaries fetched successfully",
                    "salaries" => $salary_arr["ar"]
                )
            );
        } else {
            echo json_encode(
                array(
                    "status" => 404,
                    "message" => "Not found salaries"
                )
            );
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
