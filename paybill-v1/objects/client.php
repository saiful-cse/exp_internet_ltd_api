<?php

include_once './config/database.php';

class Client
{
    /*
     * Database connection
     */
    private $conn;

    /*
     * Objects properties
     */
    public $id, $phone;

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
        $query = "SELECT clients.*, areas.area_name as area
        FROM clients
        INNER JOIN areas ON clients.area_id = areas.id 
        WHERE clients.phone = '$this->phone'";

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        //query execute
        $stmt->execute();
        return $stmt->fetch();
    }
}
