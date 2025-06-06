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

$ppp_name = $data->ppp_name;
$ppp_pass = $data->ppp_pass;
$pkg_id = $data->pkg_id;
$mode = $data->mode;

$login_ip = $data->login_ip;
$username = $data->username;
$password = $data->password;


if (!empty($ppp_name) && !empty($ppp_pass) && !empty($pkg_id) && !empty($mode)) {

    try {
        $util = new RouterOS\Util(
            $client = new RouterOS\Client($login_ip, $username, $password)
        );

        if($pkg_id === 'Regular'){
            $pkg_id = 'Basic';
        }

        if($pkg_id === 'Economy'){
            $pkg_id = 'Professional';
        }

        
        
        $util->setMenu('/ppp/secret');

        if ($util->add(
            array(
                'name' => $ppp_name,
                'password' => $ppp_pass,
                'profile' => $pkg_id,
                'disabled' => ($mode == 'Disable') ? "true" : "false",
                'service' => 'pppoe'
            )
        )) {
            echo json_encode(array(
                "status" => 200,
                "message" => "PPP Created Successfully"
            ));
        }
    } catch (\Throwable $th) {
        echo json_encode(
            array(
                "status" => 500,
                "message" => "500 Unable to connect mikrotik server, ".$th->getMessage()
            )
        );
    }
} else {
    echo json_encode(array(
        "status" => 416,
        "message" => "Data Incomplete."
    ));
}
