<?php
include_once './config/database.php';
date_default_timezone_set("Asia/Dhaka");


class Admin
{
    /*
     * Database connection
     */
    private $conn;

    /*
     * Objects properties
     */
    public $employee_id, $name, $pin, $created_at, $details;
    /*
     * Constructor with $db as database connection
     */
    public function __construct($db)
    {
        $this->conn = $db;
    }

    //admin login
    function login()
    {
        //query
        $query = "SELECT employee_id, pin FROM employees 
        WHERE employee_id = :employee_id AND pin = :pin";

        // prepare query statement
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":employee_id", $this->employee_id);
        $stmt->bindParam(":pin", $this->pin);

        $stmt->execute();

        return $stmt;
    }
 
    function login_record()
    {

        $current_date =  date("Y-m-d H:i:s");
        $query = "INSERT INTO logs 
        SET time = :current_date, details = :details";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":details", $this->details);
        $stmt->bindParam(":current_date", $current_date);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    

}
