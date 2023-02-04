<?php
include_once './config/router_config.php';
// required headers
date_default_timezone_set("Asia/Dhaka");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

//RoterOS
use PEAR2\Net\RouterOS;

require_once './PEAR2/Autoload.php';

$data = json_decode(file_get_contents("php://input"));

try {
    $util = new RouterOS\Util(
        $client = new RouterOS\Client($login_ip, $username, $password)
    );

    echo "Connected via 103.132.248.162";

    } catch (\Throwable $th) {
    echo json_encode(
        array(
            "status" => 500,
            "message" => "Unable to connect mikrotik server.",
            "log" => $th->getMessage()
        )
    );
}
