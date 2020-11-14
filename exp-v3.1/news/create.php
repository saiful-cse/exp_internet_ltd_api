<?php
// Requires headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

/*
 * include database and object files
 */
include_once '../config/database.php';
include_once  '../objects/news.php';

/*
 * Instance database and news object
 */
$database = new Database();
$db = $database->getConnection();

/*
 * Initialize object
 */
$news = new News($db);

/*
 * Defining timezone
 */
$timezone = new DateTime('now', new DateTimeZone('Asia/Dhaka'));

/*
 * creating photo path via timestamp
 */
$photo_timestamp_path = $timezone->format('d_m_y-h_i_s');

/*
 * Getting current timestamp
 */
$timestamp = $timezone->format('Y-m-d H:i:s');

/*
 * Getting data from app via POST method
 */
$title = $_POST['title'];
$description = $_POST['description'];
$image = $_POST['image'];

/*
 * Checking is empty
 */

if (!empty($title) && !empty($description) && !empty($image))
{
    //Defining image directory
    $image_path = "images/".$photo_timestamp_path.".png";

    $news->title = $title;
    $news->description = $description;
    $news->image_path = $photo_timestamp_path.".png";


    //image decode to base64
    $decoded_image = base64_decode($image);

    //inserting news to database and image move to directory
    if ($news->create() && file_put_contents($image_path, $decoded_image))
    {
        //if success the insert and move
        echo json_encode(array("message" => "News has been published successfully"));
    }else
    {
        echo json_encode(array("message" => "Something went wrong!!"));
    }
}else
{
    echo json_encode(array("message" => "Data incomplete!!"));
}



