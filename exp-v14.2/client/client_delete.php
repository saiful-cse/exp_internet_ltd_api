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


// generate json web token
include_once '../config/core.php';
include_once '../libs/php-jwt-master/src/BeforeValidException.php';
include_once '../libs/php-jwt-master/src/ExpiredException.php';
include_once '../libs/php-jwt-master/src/SignatureInvalidException.php';
include_once '../libs/php-jwt-master/src/JWT.php';

use \Firebase\JWT\JWT;


if (
    !empty($_POST['jwt']) && !empty($_POST['client_id']) && !empty($_POST['ppp_name']) && !empty($_POST['document'])
) {

    try {
        // decode jwt
        $decoded = JWT::decode($_POST['jwt'], $key, array('HS256'));

        $database = new Database();
        $db = $database->getConnection();
        $client = new Client($db);

        //Assing the value in client class

        // ------------Mikrotik Connection Start ---------

        $device = new Device($db);
        $stmt = $device->get_device_url();

        //retrieve the table contents
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            $url = $row['api_base'] . "pppDelete.php";
            $login_ip = $row['login_ip'];
            $username = $row['username'];
            $password = $row['password'];
        }

        $postdata = array(
            'ppp_name' => $_POST['ppp_name'],

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
        $mikrotik_response = json_decode($result, true);

        if ($mikrotik_response['status'] == 200) {

            if (file_exists('../documents/' . $_POST['document'])) {
                if (unlink('../documents/' . $_POST['document'])) {
                    $client->id =  $_POST['client_id'];
                    if ($client->client_details_delete()) {
                        echo json_encode(array(
                            "status" => 200,
                            "message" => "Client details deleted successfully"
                        ));
                    }
                }
            } else {
                echo json_encode(array(
                    "status" => 202,
                    "message" => "PPP deleted from mikrotik but document not exist in database"
                ));
            }
        } else {
            echo json_encode(array(
                "status" => $mikrotik_response['status'],
                "message" => $mikrotik_response['message']
            ));
        }
    } catch (\Throwable $e) {
        // tell the user access denied  & show error message
        echo json_encode(array(
            "status" => 401,
            "message" => "Access denied, error: " . $e->getMessage(),
            "error" => $e->getMessage()
        ));
    }
} else {
    echo json_encode(array(
        "status" => 416,
        "message" => "Data Incomplete."
    ));
}
