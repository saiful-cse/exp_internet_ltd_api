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

if (!empty($_POST['id'])) {

    ////////////////////////////////////
    //Fetching client information form DB
    ////////////////////////////////////
    $database = new Database();
    $db = $database->getConnection();

    //set the ppp name in query
    $employee = new Employee($db);
    $employee->id = $_POST['id'];

    $stmt = $employee->employee_details();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo json_encode(
            array(
                "id" => $row['id'],
                "employee_id" => $row['employee_id'],
                "name" => $row['name'],
                "address" => $row['address'],
                "mobile" => $row['mobile'],
                "pin" => $row['pin'],
                "about" => $row['about'],
                "employee_id" => $row['employee_id'],
                "super_admin" => $row['super_admin'],
                "dashboard" => $row['dashboard'],
                "client_add" => $row['client_add'],
                "client_details_update" => $row['client_details_update'],
                "sms" => $row['sms'],
                "txn_summary" => $row['txn_summary'],
                "txn_edit" => $row['txn_edit'],
                "upstream_bill" => $row['upstream_bill'],
                "salary_add" => $row['salary_add'],
                "device" => $row['device'],
                "note" => $row['note']
            )
        );
    }
} else {
    echo json_encode(array(
        "status" => 416,
        "message" => "Data Incomplete."
    ));
}
