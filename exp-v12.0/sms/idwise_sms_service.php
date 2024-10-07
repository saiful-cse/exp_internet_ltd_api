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
 * Initialize object
 */
$number = "8801835559161";
$message = "Testing";


function sms_send($number, $message)
{
    $url = "https://cheapsmsbd.xyz/_backend/index.php?route=extension/module/all_sms_gateway/api/sms/send";

    // Authorization token
    $token = "4LJiyhrESy47BoY4bvo9IlqzqgP8E0f4xRPZcuXi";

    // Data to be sent in the POST request
    $data = [
        "send_through" => "android",
        "type" => "plain",
        "phone" => $number,
        "message" => $message
    ];

    // Initialize cURL session
    $ch = curl_init();

    // Set cURL options
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1); // Specify this is a POST request
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data)); // Send data

    // Set the headers
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Authorization: Bearer ' . $token
    ));

    // Return the response instead of outputting it
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    // Execute the request and capture the response
    $response = curl_exec($ch);

    // Check for cURL errors
    if ($response === false) {
        echo 'Curl error: ' . curl_error($ch);
    } else {
        return $response;
    }
    // Close the cURL session
    curl_close($ch);
}

//data check
if (!empty($message) && !empty($number)) {

    $sms_send_response = json_decode(sms_send($number, $message), true);

    if ($sms_send_response['status'] === 'success') {
        echo json_encode(array(
            "status" => 200,
            "message" => "SMS sent successfully"
        ));
    } else {
        echo json_encode(array(
            "status" => 201,
            "message" => "[" . $sms_send_response['status'] . "]" .
                ", " . $sms_send_response['message']
        ));
    }
} else {
    echo json_encode(array(
        "status" => 416,
        "message" => "Data Incomplete."
    ));
}
