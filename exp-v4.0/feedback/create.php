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
include_once  '../objects/feedback.php';

/*
 * Instance database and news object
 */
$database = new Database();
$db = $database->getConnection();

/*
 * Initialize object
 */
$feedback = new Feedback($db);

$client_id = $_POST['client_id'];
$feedbackk = $_POST['feedback'];

if (!empty($client_id) && !empty($feedbackk))
{
    $feedback->client_id = $client_id;
    $feedback->feedback = $feedbackk;

    if ($feedback->create())
    {
        echo json_encode(array("message" => "Your feedback has been submitted"));

    }else
    {
        echo json_encode(array("message" => "Something wet wrong!!"));
    }
}else
{
    echo json_encode(array("message" => "Data incomplete!!"));
}