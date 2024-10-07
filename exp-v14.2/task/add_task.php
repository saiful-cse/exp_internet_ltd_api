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


if (!empty($_POST['description']) && !empty($_POST['assignBy']) && !empty($_POST['assignOn'])) {

    $task = new Task($db);

    $task->description = $_POST['description'];
    $task->assign_by = $_POST['assignBy'];
    $task->assign_on = $_POST['assignOn'];
    $task->created_at = date("Y-m-d H:i:s");

    if($task->task_add()){
        echo json_encode(array(
            "status" => 200,
            "message" => "Task added successfully."
        ));
    }
    
} else {
    echo json_encode(array(
        "status" => 416,
        "message" => "Data Incomplete."
    ));
}
