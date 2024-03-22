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
$employee->employee_id = $_POST['employee_id'];
$employee->name = $_POST['name'];
$employee->address = $_POST['address'];
$employee->mobile = $_POST['mobile'];
$employee->about = $_POST['about'];
$employee->pin = $_POST['pin'];
$employee->super_admin = $_POST['super_admin'];
$employee->dashboard = $_POST['dashboard'];
$employee->client_add = $_POST['client_add'];
$employee->client_details_update = $_POST['client_details_update'];
$employee->sms = $_POST['sms'];
$employee->txn_summary = $_POST['txn_summary'];
$employee->txn_edit = $_POST['txn_edit'];
$employee->upstream_bill = $_POST['upstream_bill'];
$employee->salary_add = $_POST['salary_add'];
$employee->device = $_POST['device'];
$employee->note = $_POST['note'];

if ($employee->employee_id_is_exist()) {
    echo json_encode(array(
        "status" => 207,
        "message" => $_POST['employee_id'] . " employee ID Used for another employee, try agian with new ID"
    ));
} else {

    if ($employee->employee_details_update()) {
        echo json_encode(array(
            "status" => 200,
            "message" => "Details Updated Successfully"
        ));
    } else {
        echo json_encode(array(
            "status" => 500,
            "message" => "Not Update."
        ));
    }
}
