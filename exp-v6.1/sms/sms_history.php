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
include_once  '../objects/sms.php';

function sms_history(){
    $database = new Database();
    $db = $database->getConnection();

    /*
     * Initialize object
     */
    $sms = new Sms($db);

    $stmt = $sms->sms_history();
    $num = $stmt->rowCount();

    if ($num>0)
    {
        //active client array
        $sms_array["sms_history"] = array();

        //retrieve the table contents
        while($row = $stmt->fetch(PDO::FETCH_ASSOC))
        {
            array_push($sms_array["sms_history"], $row);
        }
        echo json_encode($sms_array);

    }else
    {
        echo json_encode(array("message" => "No found sms history"));
    }
}

function more_sms_history($last_id){
    
    $database = new Database();
    $db = $database->getConnection();

    /*
     * Initialize object
     */
    $sms = new Sms($db);

    $sms->last_id = $last_id;

    $stmt = $sms->more_sms_history();
    $num = $stmt->rowCount();

    if ($num>0)
    {
        //active client array
        $sms_array["sms_history"] = array();

        //retrieve the table contents
        while($row = $stmt->fetch(PDO::FETCH_ASSOC))
        {
            array_push($sms_array["sms_history"], $row);
        }
        echo json_encode($sms_array);

    }else
    {
        echo json_encode(array("message" => "No found sms history"));
    }
}

    
    if (isset($_GET['last_id'])) {
        $last_id = $_GET['last_id'];
        more_sms_history($last_id);
    } else {
        sms_history();
    }

