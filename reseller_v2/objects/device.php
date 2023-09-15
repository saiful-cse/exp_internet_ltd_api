<?php
include_once '../config/database.php';
class Device
{
    /*
     * Database connection
     */
    private $conn;

    /*
     * Objects properties
     */
    public $id, $api_base, $login_ip, $username, $password;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    function get_device_url()
    {
        //query
        $query = "SELECT * FROM devices WHERE id = 1";

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        $stmt->execute();

        return $stmt;
    }


    function getApiBase()
    {
        $query1 = "SELECT api_base FROM devices WHERE id = 1";

        $stmt = $this->conn->query($query1);
        $f = $stmt->fetch();
        return $f['api_base'];
    }


}
