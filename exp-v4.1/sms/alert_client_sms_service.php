<?php 
//Requires headers
date_default_timezone_set("Asia/Dhaka");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 5");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$message = $_POST['message'];


/*
 * include database and object files
 */
include_once '../config/database.php';
include_once  '../objects/sms.php';


/*
 * Instance database and dashboard object
 */
$database = new Database();
$db = $database->getConnection();

/*
 * Initialize object
 */
$sms = new Sms($db);

//set the value for extracking phone numbers
$sms->current_date = date("Y-m-d H:i:s");

$stmt = $sms->getting_alert_client_phone();
$data = $stmt->rowCount();

if($data > 0){
    
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){

        $num[] = $row['phone'];
    }
    $numbers =  implode(', ', $num);
    
    //Set the value
    $sms->numbers = $numbers;
    $sms->msg_body = $message;
    $sms->created_at = date("Y-m-d H:i:s");

    //SMS service
    $url = "http://66.45.237.70/api.php";
    $data= array(
    'username'=>"01835559161",
    'password'=>"saiful@#21490",
    'number'=>$numbers,
    'message'=>$message
    );

    $ch = curl_init(); // Initialize cURL
    curl_setopt($ch, CURLOPT_URL,$url);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $smsresult = curl_exec($ch);

    $p = explode("|",$smsresult);
    $sendstatus = $p[0];


    switch ($sendstatus) {
        case '1000':
            echo json_encode(array("message" => "Invalid user or Password"));
            break;
        case '1002':
            echo json_encode(array("message" => "Empty Number"));
            break;
        case '1003':
            echo json_encode(array("message" => "Invalid message or empty message"));
            break;
        case '1004':
            echo json_encode(array("message" => "Invalid number"));
            break;
        case '1005':
            echo json_encode(array("message" => "All Number is Invalid"));
            break;
        case '1006':
            echo json_encode(array("message" => "Insufficient Balance"));
            break;
        case '1009':
            echo json_encode(array("message" => "Inactive Account"));
            break;
        case '1010':
            echo json_encode(array("message" => "Max number limit exceeded"));
            break;
        case '1101':
            
            if($sms->sms_status_update_and_store()){
                echo json_encode(array("message" => 200));
            }
            break;
    }

}else{
    
    echo json_encode(array("message" => "Nothing unsent alert client"));
}
