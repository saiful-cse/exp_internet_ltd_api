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

$login_ip = $_POST['login_ip'];;
$username = $_POST['username'];
$password = $_POST['password'];

require_once './PEAR2/Autoload.php';

try {
    $util = new RouterOS\Util(
        $client = new RouterOS\Client($login_ip, $username, $password)
    );

    $util->setMenu('/ppp/active');

    $client_array["ar"] = array();

    //not create another card file, adjusting same file

    foreach ($util->getAll() as $item) {

        $ar = array(
            "name" =>  "MAC: " . $item->getProperty("caller-id"),
            "phone" => "Uptime: " . $item->getProperty("uptime"),
            "ppp_name" => $item->getProperty("name")
        );
        array_push($client_array["ar"], $ar);
    }

    if (!empty($client_array["ar"])) {

        echo json_encode(
            array(
                "status" => 200,
                "message" => "Online client fetched successfully",
                "clients" => $client_array["ar"]
            )
        );
    } else {
        echo json_encode(
            array(
                "status" => 404,
                "message" => "Not found online client."
            )
        );
    }
} catch (\Throwable $th) {
    echo json_encode(
        array(
            "status" => 500,
            "message" => "Unable to connect mikrotik server."
        )
    );
}
