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
include_once  '../objects/employee.php';
include_once  '../objects/device.php';


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

$employee_id = $_POST['employee_id'];
$pin = $_POST['pin'];


if (!empty($employee_id && !empty($pin))) {

    $employee = new Employee($db);
    $employee->employee_id = $employee_id;
    $employee->pin = $pin;
    $employee->details = $employee_id . " employee login successfully";

    $stmt =  $employee->login();
    $num = $stmt->rowCount();

    $device = new Device($db);
    $stmt2 = $device->get_device_url();
    $api_base = $login_ip = $username = $password = "";

    while ($row = $stmt2->fetch(PDO::FETCH_ASSOC)) {
        $api_base = $row['api_base'];
        $login_ip = $row['login_ip'];
        $username = $row['username'];
        $password = $row['password'];
    }

    if ($num > 0) {

        $employee->login_record();
        $token = array(
            "iat" => $issued_at,
            "exp" => $expiration_time,
            "iss" => $issuer,
            "data" => $employee_id
        );

        // generate jwt
        $jwt = JWT::encode($token, $key);

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo json_encode(
                array(
                    "status" => 200,
                    "jwt" => $jwt,
                    "message" => "Login Successfully",
                    "employee_id" => $row['employee_id'],
                    "super_admin" => $row['super_admin'],
                    "dashboard" => $row['dashboard'],
                    "client_add" => $row['client_add'],
                    "client_details_update" => $row['client_details_update'],
                    "sms" => $row['sms'],
                    "txn_summary" => $row['txn_summary'],
                    "txn_edit" => $row['txn_edit'],
                    "upstream_bill" => $row['upstream_bill'],
                    "salary_add" => $row['salary_add'],
                    "device" => $row['device'],
                    "note" => $row['note'],

                    "api_base" => $api_base,
                    "login_ip" => $login_ip,
                    "username" => $username,
                    "password" => $password
                )
            );
        }
    } else {

        echo json_encode(array(
            "status" => 404,
            "message" => "Employee ID or Pin Wrong!!"
        ));
    }
} else {
    echo json_encode(array(
        "status" => 416,
        "message" => "Data Incomplete."
    ));
}
