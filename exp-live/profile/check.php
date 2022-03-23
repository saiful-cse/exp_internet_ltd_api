<?php
date_default_timezone_set("Asia/Dhaka");
include_once '../config/url_config.php';
// required headers
header("Access-Control-Allow-Origin:" . $BASE_URL . $SECOND_PATH);
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

/*
 * include database and object files
 */
include_once '../config/database.php';
include_once  '../objects/profile.php';


$data = json_decode(file_get_contents("php://input"));

if (!empty($data->phone)) {

    ////////////////////////////////////
    //Fetching client information form DB
    ////////////////////////////////////
    $database = new Database();
    $db = $database->getConnection();

    //set the ppp name in query
    $profile = new Profile($db);
    $profile->phone = $data->phone;

    if($profile->isExist()){
        echo json_encode(array(
            "status" => 200,
            "message" => "Already Registered"
        ));
    }else{
        echo json_encode(array(
            "status" => 404,
            "message" => "Not found"
        ));
    }
    
} else {
    echo json_encode(array(
        "status" => 416,
        "message" => "Data Incomplete."
    ));
}
