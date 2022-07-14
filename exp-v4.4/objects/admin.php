<?php
include_once '../config/database.php';

class Admin
{
    /*
     * Database connection
     */
    private $conn;

    /*
     * Objects properties
     */
    public $admin_id, $name, $pin, $created_at, $details;
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
        $query = "SELECT admin_id, pin FROM users 
        WHERE admin_id = :admin_id AND pin = :pin";

        // prepare query statement
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":admin_id", $this->admin_id);
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

    function fetch_logs()
    {
        //query
        $query = "SELECT * FROM logs ORDER BY time DESC LIMIT 5";

        // prepare query statement
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

}
