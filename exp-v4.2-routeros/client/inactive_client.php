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
 * Function for inactive client
 */
function inactive_client()
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

    $stmt = $client->inactive_client();
    $num = $stmt->rowCount();

    if ($num>0)
    {
        
        $client_arr["inactive_client"] = array();

        //retrieve the table contents
        while($row = $stmt->fetch(PDO::FETCH_ASSOC))
        {

            array_push($client_arr["inactive_client"], $row);
        }
        echo json_encode($client_arr);
    }else
    {
        echo json_encode(array("message" => "No found inactive client"));
    }
}

/*
 * Function for more inactive client
 */
function more_inactive_client($last_id)
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

    $client->last_id = $last_id;

    $stmt = $client->more_inactive_client();
    $num = $stmt->rowCount();

    if ($num>0)
    {
        
        $client_arr["inactive_client"] = array();

        //retrieve the table contents
        while($row = $stmt->fetch(PDO::FETCH_ASSOC))
        {
            array_push($client_arr["inactive_client"], $row);
        }
        echo json_encode($client_arr);
        
    }else
    {
        echo json_encode(array("message" => "No found more inactive client"));
    }
}

if (isset($_GET['last_id']))
{
    $last_id = $_GET['last_id'];
    more_inactive_client($last_id);

}else
{
    inactive_client();
}


