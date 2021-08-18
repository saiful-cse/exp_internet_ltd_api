<?php

//Required headers
header("Access-Control-Allow-Origin");
header("Content-Type: application/json; charset=UTF-8");

/*
 * include database and object files
 */
include_once '../config/database.php';
include_once  '../objects/news.php';


$database = new Database();
$db = $database->getConnection();

/*
 * Initialize object
 */
$news = new News($db);

$id = $_POST['id'];
$image = $_POST['image'];

if (!empty($id) && !empty($image)){

    $news->id = $id;

    if ($news->delete() && @unlink("images/".$image)){

        // tell the user
        echo json_encode(array("message" => "News has been deleted."));
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




