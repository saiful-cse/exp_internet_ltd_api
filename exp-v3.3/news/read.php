<?php

//Required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=UTF-8");

/*
 * include database and object files
 */
include_once '../config/database.php';
include_once  '../objects/news.php';


/*
 * Function for news load
 */
function news_load()
{
    /*
 * Instance database and dashboard object
 */
    $database = new Database();
    $db = $database->getConnection();

    /*
     * Initialize object
     */
    $news = new News($db);

    $stmt = $news->news_load();
    $num = $stmt->rowCount();

    if ($num>0)
    {
        //active client array
        $news_arr["news"] = array();

        //retrieve the table contents
        while($row = $stmt->fetch(PDO::FETCH_ASSOC))
        {
            extract($row);

            $each_news = array(
                "id" => $id,
                "created_at" => $created_at,
                "title" => $title,
                "description" => $description,
                "image_path" => $image_path
            );

            array_push($news_arr["news"], $each_news);
        }
        echo json_encode($news_arr);

    }else
    {
        echo json_encode(array("message" => "No found news"));
    }
}


/*
 * Function for more news load
 */
function more_news_load($last_id)
{
    /*
 * Instance database and dashboard object
 */
    $database = new Database();
    $db = $database->getConnection();

    /*
     * Initialize object
     */
    $news = new News($db);

    $news->id = $last_id;

    $stmt = $news->more_news_load();
    $num = $stmt->rowCount();

    if ($num>0)
    {
        //active client array
        $news_arr["news"] = array();

        //retrieve the table contents
        while($row = $stmt->fetch(PDO::FETCH_ASSOC))
        {
            extract($row);

            $each_news = array(
                "id" => $id,
                "created_at" => $created_at,
                "title" => $title,
                "description" => $description,
                "image_path" => $image_path
            );

            array_push($news_arr["news"], $each_news);
        }
        echo json_encode($news_arr);

    }else
    {
        echo json_encode(array("message" => "No more news found"));
    }
}




if (isset($_GET['last_id']))
{
    $last_id = $_GET['last_id'];
    more_news_load($last_id);

}else
{
    news_load();
}


