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
    public $admin_id, $name, $pin, $created_at;
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
}
