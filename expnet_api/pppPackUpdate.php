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

$data = json_decode(file_get_contents("php://input"));

$login_ip = $data->login_ip;
$username = $data->username;
$password = $data->password;

try {

    $util2 = new RouterOS\Util(
        $client = new RouterOS\Client($login_ip, $username, $password)
    );

    $util2->setMenu('/ppp/secret');

    foreach ($data->ppp_names as $pppName) {
        $util2->set(
            $util2->find($pppName),
            array(
                'profile' => $data->pack
            )
        );
    }

    echo json_encode(array(
        "status" => 200,
        "message" => "PPPs pack upadate successfully."

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
