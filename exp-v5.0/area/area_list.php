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
include_once  '../objects/area.php';

$database = new Database();
$db = $database->getConnection();

$area = new Area($db);

$stmt = $area->area_list();
$num = $stmt->rowCount();

//retrieve the table contents
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $list[] = $row;
}
echo json_encode(
    $list
);
