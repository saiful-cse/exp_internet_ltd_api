<?php

include_once '../config/database.php';
include_once '../objects/client.php';
include_once '../objects/device.php';

$database = new Database();
$db = $database->getConnection();

$pppName = new Client($db);


$stmt2 = $pppName->enablePpp();
$enable_db_ppp = array();
while ($row = $stmt2->fetch(PDO::FETCH_ASSOC)) {
    array_push($enable_db_ppp, $row['ppp_name']);
}

$url = "http://192.168.1.8/api/expert_internet_api/expnet_api/pppSecretList.php";
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
$result = curl_exec($ch);
curl_close($ch);
$data = json_decode($result);

echo "Uncommon Disable/Enable PPP: <br>";
echo json_encode(array_merge(array_diff($enable_db_ppp, $data), array_diff($data, $enable_db_ppp)));

