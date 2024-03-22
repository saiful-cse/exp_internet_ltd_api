<?php
date_default_timezone_set("Asia/Dhaka");
include_once '../config/url_config.php';
// required headers
header("Access-Control-Allow-Origin:" . $BASE_URL . $SECOND_PATH);
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/database.php';
include_once  '../objects/client.php';

if (!empty($_POST['id']) && !empty($_POST['onu_mac'])) {

    $database = new Database();
    $db = $database->getConnection();
    $client = new Client($db);

    //Assing the value in client class
    $client->id = $_POST['id'];
    $client->onu_mac = $_POST['onu_mac'];

    if ($client->onuMacUpdate()) {
        echo json_encode(array(
            "status" => 200,
            "message" => "ONU MAC Updated successfully"
        ));
    } else {
        echo json_encode(array(
            "status" => 202,
            "message" => "ONU MAC not updated!!"
        ));
    }
} else {
    echo json_encode(array(
        "status" => 416,
        "message" => "Data Incomplete."
    ));
}
