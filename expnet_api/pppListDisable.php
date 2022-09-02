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

$data = json_decode(file_get_contents("php://input"), true);

$pppName = array();

for($i = 0; $i < count($data); $i++){
    $pppName[] = $data[$i];
}

try {

    $util = new RouterOS\Util(
        $client = new RouterOS\Client($login_ip, $username, $password)
    );
    $util2 = new RouterOS\Util(
        $client = new RouterOS\Client($login_ip, $username, $password)
    );

    $util->setMenu('/ppp/active');
    $util2->setMenu('/ppp/secret');

    for ($i = 0; $i < count($pppName); $i++) {
        $util->remove(RouterOS\Query::where('name', $pppName[$i]));
        $util2->disable(RouterOS\Query::where('name', $pppName[$i]));
    }

    echo json_encode(array(
        "status" => 200,
        "message" => "PPPs disabled successfully."

    ));
} catch (\Throwable $th) {
    echo json_encode(
        array(
            "status" => 500,
            "message" => "Unable to connect mikrotik server.",
            "log" => $th->getMessage()
        )
    );
}
