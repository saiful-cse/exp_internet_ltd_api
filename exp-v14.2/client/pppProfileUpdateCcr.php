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
 * include database and object files
 */
include_once '../config/database.php';
include_once  '../objects/client.php';
include_once  '../objects/device.php';

/*
* Instance database and dashboard object
*/
$database = new Database();
$db = $database->getConnection();

$pppClient = new Client($db);

$stmt2 = $pppClient->getPppNameToPackListUpdate();

if ($stmt2->rowCount() > 0) {

    //Collecting phone numbers and ppp name
    while ($row = $stmt2->fetch(PDO::FETCH_ASSOC)) {

        $pppName[] = $row['ppp_name'];
    }

    $device = new Device($db);
    $stmt = $device->get_device_url();

    //retrieve the table contents
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        $url = $row['api_base'] . "pppPackUpdate.php";
        $login_ip = $row['login_ip'];
        $username = $row['username'];
        $password = $row['password'];
    }

    $postdata = array(
        'login_ip' => $login_ip,
        'username' => $username,
        'password' => $password,
        'ppp_names' => $pppName,
        'pack' => 'Standard'
    );

    var_dump($postdata);
    exit;

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postdata));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    $result = curl_exec($ch);
    curl_close($ch);

    //Disable and Remove form mikrotik server
    $mikrotik_response = json_decode($result, true);

    if ($mikrotik_response['status'] == 200) {

        //Set the value

        echo json_encode(array(
            "status" => $mikrotik_response['status'],
            "message" => $mikrotik_response['message']
        ));

    } else {
        echo json_encode(array(
            "status" => $mikrotik_response['status'],
            "message" => $mikrotik_response['message']
        ));
    }
}
