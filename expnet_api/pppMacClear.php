<?php
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

$ppp_name = $_POST['ppp_name'];

$login_ip = $_POST['login_ip'];
$username = $_POST['username'];
$password = $_POST['password'];

if (!empty($ppp_name)) {

    try {
        $util = new RouterOS\Util(
            $client = new RouterOS\Client($login_ip, $username, $password)
        );

        $util->setMenu('/ppp/secret');

        if ($util->set(
            $util->find($ppp_name),
            array(
                'caller-id' => ''
            )
        )) {
            echo json_encode(array(
                "status" => 200,
                "message" => "PPP MAC clear Successfully"
            ));
        }
    } catch (\Throwable $th) {
        echo json_encode(
            array(
                "status" => 500,
                "message" => "Unable to connect mikrotik server.",
                "log" => $th->getMessage()
            )
        );
    }
} else {
    echo json_encode(array(
        "status" => 416,
        "message" => "Data Incomplete."
    ));
}
