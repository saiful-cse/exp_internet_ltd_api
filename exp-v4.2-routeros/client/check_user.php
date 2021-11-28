<?php
/**
 * Created by PhpStorm.
 * User: SAIFUL
 * Date: 3/8/2019
 * Time: 11:15 PM
 */

// required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');

//Include database and object files
include_once '../config/database.php';
include_once '../objects/client.php';


// get database connection
$database = new Database();
$db = $database->getConnection();

// prepare user object
$client = new Client($db);

// set phone property of record to read
$phone = $_GET['phone'];

if (!empty($phone)){

    $client->phone = $phone;

    //Query users
    $stmt = $client->check();
    $num = $stmt->rowCount();

    if ($num>0){

        // tell the user, exist user
        echo json_encode(
            array("message" => 200, "ip" => "103.120.163.18", "ftp_server" => "http://10.16.100.244/index.php", "live_tv" => "http://10.16.100.244/livetv.php")
        );

    }else{

        // tell the user, for new registration
        echo json_encode(
            array("message" => 404, "ip" => "103.120.163.18", "ftp_server" => "http://10.16.100.244/index.php", "live_tv" => "http://10.16.100.244/livetv.php")
        );
    }

}else{
    
    // tell the user
    echo json_encode(array("message" => "Data is incomplete."));
}