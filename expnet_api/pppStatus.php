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

if (!empty($ppp_name) && !empty($login_ip) && !empty($username) && !empty($password)) {

    $status = $ppp_status = $ppp_activity = $caller_id = $connected_ip = $last_loged_out = $last_log_in = $uptime = "---";
    $download = $upload = 0;

    //Getting secret status 
    try {
        $util = new RouterOS\Util(new RouterOS\Client($login_ip, $username, $password));
        $util->setMenu('/ppp/secret');

        foreach ($util->getAll() as $item) {

            $status = 200;
            if ($item->getProperty("name") == $ppp_name) {
                $last_loged_out = $item->getProperty("last-logged-out");
		$caller_id = $item->getProperty("last-caller-id");
                $ppp_status = $item->getProperty('disabled') == "true" ? "Disable" : "Enable";
                break;
            }else{
                $ppp_status = "not found";
            }
        }
    } catch (\Throwable $th) {
        $status = 500;
        $message = "Mikrotik connection error!!".$th;

        echo json_encode(
            array(
                "status" => $status,
                "message" => $message
            )
        );
        //if the router is connecting error, next code will not execute.
        exit();
    }

    try {
        //Getting online activation
        $util2 = new RouterOS\Util(new RouterOS\Client($login_ip, $username, $password));
        $util2->setMenu('/ppp/active');
        foreach ($util2->getAll() as $item) {

            if ($item->getProperty("name") == $ppp_name) {
                $ppp_activity = "Online";
                $uptime = $item->getProperty("uptime");
                
                $connected_ip = $item->getProperty("address");
                break;
            } else {
                $ppp_activity = "Offline";
            }
        }
    } catch (\Throwable $th) {
        $status = 500;
        $message = "Mikrotik connection error!!";
    }

    try {
        //Getting online activation
        $util3 = new RouterOS\Util(new RouterOS\Client($login_ip, $username, $password));
        $util3->setMenu('interface/');
        foreach ($util3->getAll() as $item) {

            //var_dump($item);
            if ($item->getProperty("name") == "<pppoe-".$ppp_name.">") {
                $last_log_in = $item->getProperty("last-link-up-time");
                $download =  $item->getProperty("tx-byte");
                $upload = $item->getProperty("rx-byte");
                break;
            } 
        }

    } catch (\Throwable $th) {
        $status = 500;
        $message = "Mikrotik connection error!!";
    }

    echo json_encode(
        array(

            "status" => $status,
            "ppp_status" => $ppp_status,
            "ppp_activity" => $ppp_activity,
            "router_mac" => $caller_id,
            "connected_ip" => $connected_ip,
            "last_loged_out" => $last_loged_out,
            "last_log_in" => $last_log_in,
            "uptime" => $uptime,
            "download" => round(($download/1024)/1024, 2)." MB",
            "upload" => round(($upload/1024)/1024, 2)." MB"
        )
    );
} else {
    echo json_encode(array(
        "status" => 416,
        "message" => "Data Incomplete."
    ));
}
