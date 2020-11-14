<?php
include_once '../config/database.php';

class User
{
    /*
     * Database connection
     */
    private $conn;

    /*
     * Objects properties
     */
    public $userid, $name, $pin, $created_at;
    /*
     * Constructor with $db as database connection
     */
    public function __construct($db)
    {
        $this->conn = $db;
    }

    /*
    Getting users
    */
    function get_users()
    {
        //query
        $query = "SELECT userid, name, image FROM users";

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        //query execute
        $stmt->execute();
        return $stmt;
    }

    //admin login
    function login()
    {
        //query
        $query = "SELECT userid, pin FROM users 
        WHERE userid = :userid AND pin = :pin";

        // prepare query statement
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":userid", $this->userid);
        $stmt->bindParam(":pin", $this->pin);

        $stmt->execute();

        return $stmt;
    }
}
