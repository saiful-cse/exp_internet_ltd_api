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

$data = json_decode(file_get_contents("php://input"));


if (!empty($data->search_key)) {

    /*
 * Initialize object
 */
    $client = new Client($db);

    $client->search_key = $data->search_key;

    $stmt = $client->clientSearch();
    $num = $stmt->rowCount();

    if ($num > 0) {
        //active client array
        $client_arr["search_data"] = array();

        //retrieve the table contents
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            array_push($client_arr["search_data"], $row);
        }

        echo json_encode(array(
            "status" => 200,
            "message" => "Search data found",
            "clients" => $client_arr["search_data"]
        ));

    } else {

        echo json_encode(
            array(
                "status" => 404,
                "message" => "Not found search data found"
            )
        );
    }
} else {

    echo json_encode(array(
        "status" => 416,
        "message" => "Data Incomplete."
    ));
}
