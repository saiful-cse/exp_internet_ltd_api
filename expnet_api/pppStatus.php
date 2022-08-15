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

$ppp_name = $data->ppp_name;

if (!empty($ppp_name)) {

    $router_status = $router_status = $ppp_status = $ppp_activity = $caller_id = $connected_ip = $last_loged_out = $last_log_in = $uptime = "---";
    $download = $upload = 0;

    //Getting secret status 
    try {
        $util = new RouterOS\Util(new RouterOS\Client($login_ip, $username, $password));
        $util->setMenu('/ppp/secret');

        foreach ($util->getAll() as $item) {

            $router_status = 200;
            if ($item->getProperty("name") == $data->ppp_name) {
                $last_loged_out = $item->getProperty("last-logged-out");
                $ppp_status = $item->getProperty('disabled') == "true" ? "Disable" : "Enable";
                break;
            }else{
                $ppp_status = "not found";
            }
        }
    } catch (\Throwable $th) {
        $router_status = 500;
        $message = "Mikrotik connection error!!";

        echo json_encode(
            array(
                "router_status" => $router_status,
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

            if ($item->getProperty("name") == $data->ppp_name) {
                $ppp_activity = "Online";
                $uptime = $item->getProperty("uptime");
                $caller_id = $item->getProperty("caller-id");
                $connected_ip = $item->getProperty("address");
                break;
            } else {
                $ppp_activity = "Offline";
            }
        }
    } catch (\Throwable $th) {
        $router_status = 500;
        $message = "Mikrotik connection error!!";
    }

    try {
        //Getting online activation
        $util3 = new RouterOS\Util(new RouterOS\Client($login_ip, $username, $password));
        $util3->setMenu('interface/');
        foreach ($util3->getAll() as $item) {

            //var_dump($item);
            if ($item->getProperty("name") == "<pppoe-".$data->ppp_name.">") {
                $last_log_in = $item->getProperty("last-link-up-time");
                $download =  $item->getProperty("tx-byte");
                $upload = $item->getProperty("rx-byte");
                break;
            } 
        }


    } catch (\Throwable $th) {
        $router_status = 500;
        $message = "Mikrotik connection error!!";
    }

    echo json_encode(
        array(

            "router_status" => $router_status,
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
