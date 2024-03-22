<?php
date_default_timezone_set("Asia/Dhaka");
include_once '../config/url_config.php';
// required headers
header("Access-Control-Allow-Origin:" . $BASE_URL . $SECOND_PATH);
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

/*
 * include database and object files
 */
include_once '../config/database.php';
include_once  '../objects/task.php';

$database = new Database();
$db = $database->getConnection();


if (!empty($_GET['taskId'])) {

    $task = new Task($db);

    $task->id = $_GET['taskId'];
    $task->completed_at = date("Y-m-d H:i:s");

    if($task->task_completed()){
        echo json_encode(array(
            "status" => 200,
            "message" => "Task has been completed."
        ));
    }
    
} else {
    echo json_encode(array(
        "status" => 416,
        "message" => "Data Incomplete."
    ));
}
