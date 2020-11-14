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
include_once  '../objects/notice.php';

/*
 * Instance database and news object
 */
$database = new Database();
$db = $database->getConnection();

/*
 * Initialize object
 */
$notice = new Notice($db);

$noticee = $_POST['notice'];

if (!empty($noticee))
{
    $notice->notice = $noticee;


    if ($notice->create())
    {
        echo json_encode(array("message" => "Your notice has been successfully."));

    }else
    {
        echo json_encode(array("message" => "Something wet wrong!!"));
    }
}else
{
    echo json_encode(array("message" => "Data incomplete!!"));
}