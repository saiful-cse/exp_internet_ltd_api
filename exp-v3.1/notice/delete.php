<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

/*
 * include database and object files
 */
include_once '../config/database.php';
include_once  '../objects/notice.php';


$database = new Database();
$db = $database->getConnection();

/*
 * Initialize object
 */
$notice = new Notice($db);

$id = $_GET['id'];

if (!empty($id)){

    $notice->id = $id;

    if ($notice->delete()){

        // tell the user
        echo json_encode(array("message" => "Notice has been deleted."));
    }
    // if unable to create the user, tell the user
    else{

        // tell the user
        echo json_encode(array("message:" => "Unable to delete news"));

    }

}else{

    // tell the user
    echo json_encode(array("message" => "Data is incomplete."));

}




