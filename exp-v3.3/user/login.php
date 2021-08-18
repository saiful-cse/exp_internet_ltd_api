<?php

//Required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=UTF-8");

/*
 * include database and object files
 */
include_once '../config/database.php';
include_once  '../objects/user.php';

/*
 * Instance database and dashboard object
 */
$database = new Database();
$db = $database->getConnection();

/*
* Initialize object
*/
$userid = $_POST['userid'];
$pin = $_POST['pin'];

if (!empty($userid && !empty($pin))) {
    
    $user = new User($db);

    $user->userid = $userid;
    $user->pin = $pin;
    
    $stmt =  $user->login();
    $num = $stmt->rowCount();

    if ($num > 0) {

        echo json_encode(array("message" => 200));

    }else{

        echo json_encode(array("message" => "Invalid user id or pin, try again."));
    }

} else {
    echo json_encode(array("message" => "Data incomplete"));
}
