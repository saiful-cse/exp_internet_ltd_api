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

//getting current timestamp
$current_date = date("d_m_y-h_i_s");


/*
 * Getting data from app via POST method
 */
$id = $_POST['id'];
$title = $_POST['title'];
$description = $_POST['description'];
$image = $_POST['image'];
$new_image = $_POST['new_image'];

/*
 * Checking is empty
 */

if (!empty($id) && !empty($title) && !empty($description) && !empty($image) && !empty($new_image))
{

    if ($image != $new_image)
    {

        if(file_exists("images/".$image))
        {

            if (@unlink("images/".$image))
            {

                //Defining image directory
                $image_path = "images/".$current_date.".png";

                //set the data to news class
                $news->id = $id;
                $news->title = $title;
                $news->description = $description;
                $news->image_path = $current_date.".png";

                //image decode to base64
                $decoded_image = base64_decode($new_image);

                //inserting news to database and image move to directory
                if ($news->update() && file_put_contents($image_path, $decoded_image))
                {
                    //if success the insert and move
                    echo json_encode(array("message" => "News has been updated successfully"));
                }else
                {
                    echo json_encode(array("message" => "Something went wrong!!"));
                }

            }else
            {
                echo json_encode(array("message" => "image not delete"));
            }

        }else
        {
            echo json_encode(array("message" => "photo not exist on database"));
        }

    }else
    {
        //set the data to news class
        $news->id = $id;
        $news->title = $title;
        $news->description = $description;
        $news->image_path = $image;

        //inserting news to database and image move to directory
        if ($news->update())
        {
            //if success the insert and move
            echo json_encode(array("message" => "News has been updated successfully"));
        }else
        {
            echo json_encode(array("message" => "Something went wrong!!"));
        }


    }

}else
{
    echo json_encode(array("message" => "Data incomplete!!"));
}



