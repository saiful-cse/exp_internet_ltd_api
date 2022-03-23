<?php
date_default_timezone_set("Asia/Dhaka");
include_once '../config/database.php';

class Profile
{
    /*
     * Database connection
     */
    private $conn;

    /*
     * Objects properties
     */
    public $id, $registered, $mode, $name, $phone, $area,
        $ppp_name, $ppp_pass,
        $pkg_id, $reg_date, $expire_date;

    /*
     * Constructor with $db as database connection
     */
    public function __construct($db)
    {
        $this->conn = $db;
    }


    function client_details()
    {

        //query
        $query = "SELECT * FROM clients WHERE phone = '$this->phone'";

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        //query execute
        $stmt->execute();
        return $stmt;
    }

    //check mobile number exists
    function isExist()
    {
        //query
        $query = "SELECT phone FROM clients WHERE phone = '$this->phone'";

        // prepare the query
        $stmt = $this->conn->prepare($query);

        // execute the query
        $stmt->execute();
        // get number of rows
        $num = $stmt->rowCount();

        if ($num > 0) {    
            return true;
        }
        return false;
    }

    function clientRegistration()
    {
        $current_date =  date("Y-m-d H:i:s");
        $query = "INSERT INTO clients 
        SET registered = '0', name = :name, phone = :phone, reg_date = :reg_date";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":phone", $this->phone);
        $stmt->bindParam(":reg_date", $current_date);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

}
