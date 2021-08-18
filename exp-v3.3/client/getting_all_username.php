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
 * Instance database and dashboard object
 */
$database = new Database();
$db = $database->getConnection();

/*
 * Initialize object
 */
$client = new Client($db);

$stmt = $client->username();
$num = $stmt->rowCount();

if ($num>0)
{

    //retrieve the table contents
    while($row = $stmt->fetch(PDO::FETCH_ASSOC))
    {
        $d[] = $row;
    }
    $data = ['username' => $d];
    echo json_encode($data);

}else
{
    echo json_encode(array("message" => "No found username"));
}


