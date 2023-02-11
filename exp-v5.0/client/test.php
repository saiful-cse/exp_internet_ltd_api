<?php

$url = 'http://103.132.248.162/api/expert_internet_api/expnet_api/pppAction.php';
$data = array(
    'ppp_name' => 'ss-expnet-192',
    'action_type' => 'disable'
);
$postdata = json_encode($data);

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
$result = curl_exec($ch);
curl_close($ch);
//return $result;


print_r($result);
