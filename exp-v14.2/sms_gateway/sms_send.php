<?php


function all_sms_gateway_send($numbers, $message)
{
    $url = "https://cheapsmsbd.xyz/_backend/index.php?route=extension/module/all_sms_gateway/api/sms/send";
    $token = "woQRb64J3DDAf2aO1buZEq4ecPYDe8cIcLQCHmE8";

    // Data to be sent in the POST request
    $data = [
        "send_through" => "android",
        "type" => "plain",
        "phone" => $numbers,
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

function bulkbd_sms_send($numbers, $message)
{
    $url = "http://bulksmsbd.net/api/smsapi";
    $key = "WQXz2dzA4yOM3Xo2J1AP";
    $senderid = "8809617611745";

    $data = [
        "api_key" => $key,
        "senderid" => $senderid,
        "number" => $numbers,
        "message" => $message
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}
