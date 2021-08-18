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
include_once  '../objects/client.php';


/*
 * Function for active client
 */
function active_client()
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

    $stmt = $client->active_client();
    $num = $stmt->rowCount();

    if ($num>0)
    {
        //active client array
        $client_arr["active_client"] = array();

        //retrieve the table contents
        while($row = $stmt->fetch(PDO::FETCH_ASSOC))
        {
            array_push($client_arr["active_client"], $row);
        }
        echo json_encode($client_arr);

    }else
    {
        echo json_encode(array("message" => "No found active client"));
    }
}


/*
 * Function for more active client
 */
function more_active_client($last_id)
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

    $stmt = $client->more_active_client();
    $num = $stmt->rowCount();

    if ($num>0)
    {
        //active client array
        $client_arr["active_client"] = array();

        //retrieve the table contents
        while($row = $stmt->fetch(PDO::FETCH_ASSOC))
        {
           
            array_push($client_arr["active_client"], $row);
        }
        echo json_encode($client_arr);

    }else
    {
        echo json_encode(array("message" => "No found more active client"));
    }
}



if (isset($_GET['last_id']))
{
    $last_id = $_GET['last_id'];
    more_active_client($last_id);

}else
{
    active_client();
}


