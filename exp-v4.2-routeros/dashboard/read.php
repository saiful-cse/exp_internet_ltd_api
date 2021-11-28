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
include_once  '../objects/dashboard.php';


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
$dashboard = new Dashboard($db);

$jwt = $_POST['jwt'];

if (!empty($jwt)) {

    try {

        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));

        //Getting counted value form database
        $total_enabled_client = $dashboard->count_total_enabled_client();
        $total_disabled_client = $dashboard->count_total_disabled_client();
        $current_month_total_credit = $dashboard->current_month_total_credit();
        $current_month_total_debit = $dashboard->current_month_total_debit();
        $overall_credit = $dashboard->overall_credit();
        $overall_debit = $dashboard->overall_debit();

        $count_monthly_client = $dashboard->count_monthly_client();
        while ($row = $count_monthly_client->fetch(PDO::FETCH_ASSOC)) {

            $data[] = $row;
        }

        $total_invest = $dashboard->total_invest();
        while ($row = $total_invest->fetch(PDO::FETCH_ASSOC)) {

            $saifulinvest = $row['saiful'];
            $misbainvest = $row['misba'];
        }

        //JSON encode
        echo json_encode(
            array(
                "status" => 200,
                "monthly_client_count" => $data,
                "total_enabled_client" => $total_enabled_client,
                "total_disabled_client" => $total_disabled_client,
                "current_month_total_credit" => $current_month_total_credit,
                "current_month_total_debit" => $current_month_total_debit,
                "overall_credit" => $overall_credit,
                "overall_debit" => $overall_debit,
                "saifulinvest" => $saifulinvest,
                "misbainvest" => $misbainvest
            )
        );

    } catch (\Throwable $th) {
        echo json_encode(array(
            "status" => 401,
            "message" => "Access denied",
            "error" => $th->getMessage()
        ));
    }
} else {
    echo json_encode(array(
        "status" => 416,
        "message" => "Data Incomplete."
    ));
}
