<?php 
$url = "https://cheapsmsbd.xyz/_backend/index.php?route=extension/module/all_sms_gateway/api/sms/send";

// Authorization token
$token = "4LJiyhrESy47BoY4bvo9IlqzqgP8E0f4xRPZcuXi";

// Data to be sent in the POST request
$data = [
    "send_through" => "android",
    "type" => "plain",
    "phone" => "8801835559161",
    "message" => "This is a test message"
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
