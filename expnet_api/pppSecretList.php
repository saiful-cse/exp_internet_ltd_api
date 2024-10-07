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

$data = json_decode(file_get_contents("php://input"));

$login_ip = $data->login_ip;
$username = $data->username;
$password = $data->password;

require_once './PEAR2/Autoload.php';

try {
    $util = new RouterOS\Util(
        $client = new RouterOS\Client($login_ip, $username, $password)
    );

    $util->setMenu('/ppp/secret');

    $secret_ppp = array();

    foreach ($util->getAll() as $item) {

        if ($item->getProperty('disabled') == "false") {
            array_push($secret_ppp,  $item->getProperty("name"));
        }
    }
    echo json_encode(
        array(
            "status" => 200,
            "secret" => $secret_ppp
        )
    );

} catch (\Throwable $th) {
    echo json_encode(
        array(
            "status" => 500,
            "message" => "Unable to connect mikrotik server."
        )
    );
}
