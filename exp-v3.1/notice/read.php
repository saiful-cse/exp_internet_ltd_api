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
include_once  '../objects/notice.php';


/*
 * Function for notice load
 */
function notice_load()
{
    /*
 * Instance database and dashboard object
 */
    $database = new Database();
    $db = $database->getConnection();

    /*
     * Initialize object
     */
    $notice = new Notice($db);

    $stmt = $notice->notice_load();
    $num = $stmt->rowCount();

    if ($num>0)
    {
        //active client array
        $notice_arr["notice"] = array();

        //retrieve the table contents
        while($row = $stmt->fetch(PDO::FETCH_ASSOC))
        {
            extract($row);

            $each_notice = array(
                "id" => $id,
                "notice" => $notice,
                "created_at" => $created_at
            );

            array_push($notice_arr["notice"], $each_notice);
        }
        echo json_encode($notice_arr);

    }else
    {
        echo json_encode(array("message" => "No found notice"));
    }
}


/*
 * Function for more notice load
 */
function more_notice_load($last_id)
{
    /*
 * Instance database and dashboard object
 */
    $database = new Database();
    $db = $database->getConnection();

    /*
     * Initialize object
     */
    $notice = new Notice($db);

    $notice->id = $last_id;

    $stmt = $notice->more_notice_load();
    $num = $stmt->rowCount();

    if ($num>0)
    {
        //active client array
        $notice_arr["notice"] = array();

        //retrieve the table contents
        while($row = $stmt->fetch(PDO::FETCH_ASSOC))
        {
            extract($row);

            $each_notice = array(
                "id" => $id,
                "notice" => $notice,
                "created_at" => $created_at
            );

            array_push($notice_arr["notice"], $each_notice);
        }
        echo json_encode($notice_arr);

    }else
    {
        echo json_encode(array("message" => "No more found notice"));
    }
}




if (isset($_GET['last_id']))
{
    $last_id = $_GET['last_id'];
    more_notice_load($last_id);

}else
{
    notice_load();
}


