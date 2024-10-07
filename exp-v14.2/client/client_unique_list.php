<?php
include_once '../config/database.php';
include_once  '../objects/client.php';
include_once  '../objects/device.php';

$database = new Database();
$db = $database->getConnection();

$client = new Client($db);

$stmt = $client->allClient();


//retrieve the table contents
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $app_pppName[] = $row['ppp_name'];
}

$device = new Device($db);
$stmt = $device->get_device_url();

//retrieve the table contents
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

    $url = $row['api_base'] . "pppSecretList.php";
    $login_ip = $row['login_ip'];
    $username = $row['username'];
    $password = $row['password'];
}

$postdata = array(
    'login_ip' => $login_ip,
    'username' => $username,
    'password' => $password
);

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postdata, true));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
$result = curl_exec($ch);
curl_close($ch);

//Mikrotik Response handling
$mikrotik_response = json_decode($result, true);

// Find unique ppp names using array_diff()
$uniqueInArray1 = array_diff($app_pppName, $mikrotik_response['secret']); // Names in Array1 but not in Array2
$uniqueInArray2 = array_diff($mikrotik_response['secret'], $app_pppName); // Names in Array2 but not in Array1

// Merge the unique results
$uniquePpp = array_merge($uniqueInArray1, $uniqueInArray2);

// Output the unique ppp names
echo json_encode($uniquePpp);

