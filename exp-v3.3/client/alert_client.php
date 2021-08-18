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
function alert_client()
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

    $total_alert_client = $client->count_alert_client();
    $stmt = $client->alert_client();
    $num = $stmt->rowCount();

    if ($num>0)
    {

        $client_arr["alert_client"] = array();

        while($row = $stmt->fetch(PDO::FETCH_ASSOC))
        {
            
            $d[] = $row;

        }
        $data = ['total' => $total_alert_client, 'alert_client' => $d];
        echo json_encode($data);

    }else
    {
        echo json_encode(array("message" => "No found alert client"));
    }
}


/*
 * Function for more alert client
 */
function more_alert_client($last_id)
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

    $stmt = $client->more_alert_client();
    $num = $stmt->rowCount();

    if ($num>0)
    {

        $client_arr["alert_client"] = array();

        //retrieve the table contents
        while($row = $stmt->fetch(PDO::FETCH_ASSOC))
        {
            array_push($client_arr["alert_client"], $row);
        }
        echo json_encode($client_arr);

    }else
    {
        echo json_encode(array("message" => "No found more alert client"));
    }
}



if (isset($_GET['last_id']))
{
    $last_id = $_GET['last_id'];
    more_alert_client($last_id);

}else
{
    alert_client();
}


