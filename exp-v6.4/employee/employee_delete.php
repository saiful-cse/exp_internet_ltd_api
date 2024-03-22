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
include_once  '../objects/employee.php';

$database = new Database();
$db = $database->getConnection();

$employee = new Employee($db);
$employee->id = $_POST['id'];
if($employee->employee_delete()){
    echo json_encode(array(
        "status" => 200,
        "message" => "Employee deleted Successfully"
    ));
}