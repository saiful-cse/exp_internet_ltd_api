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
include_once  '../objects/feedback.php';


/*
 * Function for feedback load
 */
function feedback_load()
{
    /*
 * Instance database and dashboard object
 */
    $database = new Database();
    $db = $database->getConnection();

    /*
     * Initialize object
     */
    $feedback = new Feedback($db);

    $stmt = $feedback->feedback_load();
    $num = $stmt->rowCount();

    if ($num>0)
    {
        //active client array
        $feedback_arr["feedback"] = array();

        //retrieve the table contents
        while($row = $stmt->fetch(PDO::FETCH_ASSOC))
        {
            extract($row);

            $each_feedback = array(
                "id" => $id,
                "created_at" => $created_at,
                "client_id" => $client_id,
                "feedback" => $feedback
            );

            array_push($feedback_arr["feedback"], $each_feedback);
        }
        echo json_encode($feedback_arr);

    }else
    {
        echo json_encode(array("message" => "No found feedback"));
    }
}


/*
 * Function for more feedback load
 */
function more_feedback_load($last_id)
{
    /*
 * Instance database and dashboard object
 */
    $database = new Database();
    $db = $database->getConnection();

    /*
     * Initialize object
     */
    $feedback = new Feedback($db);

    $feedback->id = $last_id;


    $stmt = $feedback->more_feedback_load();
    $num = $stmt->rowCount();

    if ($num>0)
    {
        //active client array
        $feedback_arr["feedback"] = array();

        //retrieve the table contents
        while($row = $stmt->fetch(PDO::FETCH_ASSOC))
        {
            extract($row);

            $each_feedback = array(
                "id" => $id,
                "created_at" => $created_at,
                "client_id" => $client_id,
                "feedback" => $feedback
            );

            array_push($feedback_arr["feedback"], $each_feedback);
        }
        echo json_encode($feedback_arr);

    }else
    {
        echo json_encode(array("message" => "No found feedback"));
    }
}




if (isset($_GET['last_id']))
{
    $last_id = $_GET['last_id'];
    more_feedback_load($last_id);

}else
{
    feedback_load();
}


