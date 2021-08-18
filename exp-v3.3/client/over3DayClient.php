<?php

//Required headers
date_default_timezone_set("Asia/Dhaka");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=UTF-8");

/*
 * include database and object files
 */
include_once '../config/database.php';
include_once  '../objects/client.php';


/*
 * Function for alert client
 */
function over3Day_client()
{
    /*
 * Instance database and dashboard object
 */
    $database = new Database();
    $db = $database->getConnection();

    /*
     * Initialize object
     */
    $client = new Client($db);

    //Set the value on client class
    $client->current_date = date("Y-m-d H:i:s");

    $stmt = $client->over3Day_client();
    $num = $stmt->rowCount();

    if ($num > 0) {
        //active client array
        $client_arr["over3Day_client"] = array();

        //retrieve the table contents
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            array_push($client_arr["over3Day_client"], $row);
        }
        echo json_encode($client_arr);
    } else {
        echo json_encode(array("message" => "No found over 3 Day client"));
    }
}


/*
 * Function for more alert client
 */
function more_over3Day_client($last_id)
{
    /*
 * Instance database and dashboard object
 */
    $database = new Database();
    $db = $database->getConnection();

    /*
     * Initialize object
     */
    $client = new Client($db);

    //set the last_id to client class
    $client->last_id = $last_id;
    $client->current_date = date("Y-m-d H:i:s");

    $stmt = $client->more_over3Day_client();
    $num = $stmt->rowCount();

    if ($num > 0) {
        //active client array
        $client_arr["over3Day_client"] = array();

        //retrieve the table contents
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            array_push($client_arr["over3Day_client"], $row);
        }
        echo json_encode($client_arr);
    } else {
        echo json_encode(array("message" => "No found over 3 Day client"));
    }
}



if (isset($_GET['last_id'])) {
    $last_id = $_GET['last_id'];
    more_over3Day_client($last_id);
} else {
    over3Day_client();
}
