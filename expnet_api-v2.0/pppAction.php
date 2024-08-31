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
$action_type = $_POST['action_type'];
$login_ip = $_POST['login_ip'];
$username = $_POST['username'];
$password = $_POST['password'];

if (!empty($ppp_name) && !empty($action_type)) {

    if ($action_type === "enable") {

        try {
            $util = new RouterOS\Util(
                $client = new RouterOS\Client($login_ip, $username, $password)
            );

            $util->setMenu('/ppp/secret');

            if ($util->enable(RouterOS\Query::where('name', $ppp_name))) {
                echo json_encode(array(
                    "status" => 200,
                    "message" => "PPP Enabled successfully"
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
    } else if ($action_type == "disable") {

        try {
            $util = new RouterOS\Util(
                $client = new RouterOS\Client($login_ip, $username, $password)
            );

            $util2 = new RouterOS\Util(
                $client = new RouterOS\Client($login_ip, $username, $password)
            );

            $util->setMenu('/ppp/active');
            $util2->setMenu('/ppp/secret');

            if (
                $util->remove(RouterOS\Query::where('name', $ppp_name)) &&
                $util2->disable(RouterOS\Query::where('name', $ppp_name))
            ) {

                echo json_encode(array(
                    "status" => 200,
                    "message" => "PPP Disabled successfully"
                ));
            }
        } catch (\Throwable $th) {
            echo json_encode(
                array(
                    "status" => 500,
                    "message" => "Unable to connect mikrotik server.",
                )
            );
        }
    }
} else {
    echo json_encode(array(
        "status" => 416,
        "message" => "Data Incomplete."
    ));
}
