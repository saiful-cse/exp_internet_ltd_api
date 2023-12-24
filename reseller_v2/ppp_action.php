<?php
include('./config/database.php');
include('./objects/device.php');

$database = new Database();
$db = $database->getConnection();

$device = new Device($db);
$stmt = $device->get_device_url();

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

    $url = $row['api_base'] . "pppAction.php";
    $login_ip = $row['login_ip'];
    $username = $row['username'];
    $password = $row['password'];
}
$postdata = array(
    'ppp_name' => $_POST['ppp_name'],
    'action_type' => $_POST['action_type'],
    'login_ip' => $login_ip,
    'username' => $username,
    'password' => $password
);

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: multipart/form-data'));
$result = curl_exec($ch);
curl_close($ch);
$mikrotik_response = json_decode($result, true);

if ($mikrotik_response['status'] == 200) {
    echo json_encode(array(
        "status" => 200,
        "message" => "Success"
    ));
} else {
    echo json_encode(array(
        "status" => 400,
        "message" => "Error"
    ));
}
