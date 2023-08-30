<?php

include_once '../config/database.php';
include_once  '../objects/sms.php';
include_once  '../objects/device.php';


$database = new Database();
$db = $database->getConnection();
$sms = new Sms($db);
$stmt = $sms->getExpiredClientsPhonePPPname();

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

    $num[] = $row['phone'];
    $pppName[] = $row['ppp_name'];
}
$numbers =  implode(', ', $num);

$device = new Device($db);
$stmt = $device->get_device_url();

//retrieve the table contents
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

    $url = $row['api_base'] . "pppCreate.php";
    $login_ip = $row['login_ip'];
    $username = $row['username'];
    $password = $row['password'];
}

$data = array(
    'login_ip' => $login_ip,
    'username' => $username,
    'password' => $password,
    'ppp_names' => $pppName
);

echo $encoded = json_encode($data);

// $decoded = json_decode($encoded);

// //===========================
// $pp = array();
// foreach ($decoded->ppp_names as $pppName) {
//     $pp[] = $pppName;
// }
// //echo json_encode($pp);
// for ($i = 0; $i < count($pp); $i++) {
//     echo $pp[$i]."<br>";
// }
