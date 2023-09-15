<?php
session_start();
include('./config.php');

$token = $_SESSION['token'];
$paymentID = $_SESSION['paymentID'];

$url = curl_init($executeURL);

$header = array(
    'Content-Type:application/json',
    'authorization:' . $token,
    'x-app-key:' . $app_key
);

$post_token = array(
    'paymentID' => $paymentID
);
$post_data = json_encode($post_token);

curl_setopt($url, CURLOPT_HTTPHEADER, $header);
curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
curl_setopt($url, CURLOPT_POSTFIELDS, $post_data);
curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
//curl_setopt($url, CURLOPT_PROXY, $proxy);

$resultdata = curl_exec($url);
curl_close($url);
echo $resultdata;

