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
include_once  '../objects/profile.php';


$data = json_decode(file_get_contents("php://input"));

if (!empty($data->phone)){

    ////////////////////////////////////
    //Fetching client information form DB
    ////////////////////////////////////
    $database = new Database();
    $db = $database->getConnection();

    //set the ppp name in query
    $profile = new Profile($db);
    $profile->phone = $data->phone;

    $stmt = $profile->client_details();

    if ($stmt->rowCount() > 0) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $status = 200;
            $message = "Client details fetched successfully";
            $mode = $row['mode'];

            $name = $row['name'];
            $phone = $row['phone'];
            $area = $row['area'];

            $ppp_name = $row['ppp_name'];
            $ppp_pass = $row['ppp_pass'];

            $pkg_id = $row['pkg_id'];
            $reg_date = $row['reg_date'];
            $expire_date = $row['expire_date'];
        }
    } else {


        $name = $phone = $area = $mode = 
        $ppp_name = $ppp_pass = $pkg_id = $reg_date = $expire_date = "";

        $message = "Not found client details";
        $status = 404;
    }
    ///////////////// END //////////////////

    //Printing data
    echo json_encode(
        array(
            "status" => $status,
            "message" => $message,
            "mode" => $mode,

            "name" => $name,
            "phone" => $phone,
            "area" => $area,

            "ppp_name" => $ppp_name,
            "ppp_pass" => $ppp_pass,

            "pkg_id" => $pkg_id,
            "reg_date" => $reg_date,
            "expire_date" => $expire_date

        )
    );
} else {
    echo json_encode(array(
        "status" => 416,
        "message" => "Data Incomplete."
    ));
}
