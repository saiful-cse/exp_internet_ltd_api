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


if (!empty($_GET['employee_id'])) {

    $task = new Task($db);

    $task->employee_id = $_GET['employee_id'];

    $stmt = $task->get_pending_task();
    $num = $stmt->rowCount();

    if ($num > 0) {

        $task_arr["ar"] = array();

        //retrieve the table contents
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            array_push($task_arr["ar"], $row);
        }
        echo json_encode(
            array(
                "status" => 200,
                "message" => "Pending tasks fetched successfully",
                "tasks" => $task_arr["ar"]
            )
        );

        
    } else {
        echo json_encode(
            array(
                "status" => 404,
                "message" => "Not found pending task"
            )
        );
    }
} else {
    echo json_encode(array(
        "status" => 416,
        "message" => "Data Incomplete."
    ));
}
