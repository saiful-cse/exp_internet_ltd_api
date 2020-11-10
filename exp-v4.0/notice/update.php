<?php
// Requires headers
date_default_timezone_set("Asia/Dhaka");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

/*
 * include database and object files
 */
include_once '../config/database.php';
include_once  '../objects/notice.php';

/*
 * Instance database and news object
 */
$database = new Database();
$db = $database->getConnection();

/*
 * Initialize object
 */
$notice = new Notice($db);


/*
 * Getting data from app via POST method
 */
$id = $_POST['id'];
$notice_desc = $_POST['notice'];

/*
 * Checking is empty
 */

if (!empty($id) && !empty($notice_desc)){

    $notice->id = $id;
    $notice->notice = $notice_desc;

    if ($notice->update()){

        // tell the user
        echo json_encode(array("message" => "Notice has been updated"));
    }
    // if unable to create the user, tell the user
    else{

        // tell the user
        echo json_encode(array("message:" => "Unable to update notice"));

    }

}else{

    // tell the user
    echo json_encode(array("message" => "Data is incomplete."));

}


