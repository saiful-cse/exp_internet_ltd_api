<?php
//Required headers
date_default_timezone_set("Asia/Dhaka");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=UTF-8");

/*
 * include database and object files
 */
include_once '../config/database.php';
include_once  '../objects/dashboard.php';

/*
 * Instance database and dashboard object
 */
$database = new Database();
$db = $database->getConnection();

/*
 * Initialize object
 */
$dashboard = new Dashboard($db);

//Getting counted value form database
$total_active_client = $dashboard->count_total_active_client();
$total_inactive_client = $dashboard->count_total_inactive_client();
$current_month_total_credit = $dashboard->current_month_total_credit();
$current_month_total_debit = $dashboard->current_month_total_debit();
$overall_credit = $dashboard->overall_credit();
$overall_debit = $dashboard->overall_debit();

$count_monthly_client = $dashboard->count_monthly_client();
while ($row = $count_monthly_client->fetch(PDO::FETCH_ASSOC)) {

    $data[] = $row;
}

$total_invest = $dashboard->total_invest();
while ($row = $total_invest->fetch(PDO::FETCH_ASSOC)) {

    $saifulinvest = $row['saiful'];
    $misbainvest = $row['misba'];
}

//JSON encode
echo json_encode(
    array(
        "monthly_client_count" => $data,
        "total_active_client" => $total_active_client,
        "total_inactive_client" => $total_inactive_client,
        "current_month_total_credit" => $current_month_total_credit,
        "current_month_total_debit" => $current_month_total_debit,
        "overall_credit" => $overall_credit,
        "overall_debit" => $overall_debit,
        "saifulinvest" => $saifulinvest,
        "misbainvest" => $misbainvest
    )
);