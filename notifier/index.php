<?php
date_default_timezone_set("Asia/Dhaka");
$data = json_decode(file_get_contents("php://input"));

function sms_send($numbers, $message)
{
    $sms_api_url = "http://bulksmsbd.net/api/smsapi";
    $sms_api_key = "WQXz2dzA4yOM3Xo2J1AP";
    $sms_api_senderid = "8809617611745";
    $data = [
        "api_key" => $sms_api_key,
        "senderid" => $sms_api_senderid,
        "number" => $numbers,
        "message" => $message
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $sms_api_url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}

if (!empty($data->status && $data->interface && $data->pop)) {


    if ($data->pop === 'Khrulia') {
        $numbers = "01400559161";
    } else if ($data->pop === 'Osman') {
        $numbers = "01835559161";
    }else{
        exit();
    }

    $message = "=== Link " . $data->status . "===\nPoP" . $data->pop . "\nInterface: " . $data->interface . "\nTime: " . date("Y-m-d H:i:s");
    
    $sms_send_response = json_decode(sms_send($numbers, $message), true);

    if ($sms_send_response['response_code'] == 202) {
        echo json_encode(array(
            "status" => 200,
            "message" => "SMS sent successfully"
        ));
    } else {
        echo json_encode(array(
            "status" => 201,
            "message" => "[" . $sms_send_response['response_code'] . "]" .
                ", " . $sms_send_response['error_message']
        ));
    }
} else {
    echo json_encode(array(
        "status" => 416,
        "message" => "Data not complete"
    ));
}
