<?php
date_default_timezone_set("Asia/Dhaka");
include_once './config/database.php';

class Client
{
    /*
     * Database connection
     */
    private $conn;

    

    /*
     * Constructor with $db as database connection
     */
    public function __construct($db)
    {
        $this->conn = $db;
    }

    function registered_client($zone)
    {

        //query
        $query = "SELECT clients.*, areas.area_name as area
        FROM clients
        INNER JOIN areas ON clients.area_id = areas.id
        WHERE registered = '1' AND zone = '$zone'
        ORDER BY reg_date DESC";

        //query execute
        $stmt = $this->conn->prepare($query);

        //query execute
        $stmt->execute();
        return $stmt;
    }

    function expired_client_list($zone)
    {

        $current_date =  date("Y-m-d H:i:s");
        //query
        $query = "SELECT clients.*, areas.area_name as area
        FROM clients
        INNER JOIN areas ON clients.area_id = areas.id
        WHERE expire_date <= '$current_date' AND registered = '1' AND zone = '$zone'
        ORDER BY reg_date DESC";
        
        //query execute
        $stmt = $this->conn->prepare($query);

        //query execute
        $stmt->execute();
        return $stmt;
    }

    function count_total_expired_client($zone)
    {
        $current_date = date("Y-m-d H:i:s");
        $query = "SELECT COUNT(*) FROM clients WHERE expire_date <= '$current_date' AND zone = '$zone' AND registered = 1";

        //prepare query
        $stmt = $this->conn->prepare($query);

        //execute query
        if ($stmt->execute())
        {
            return $stmt->fetchColumn();
        }

        return false;
    }

    function count_total_client($zone)
    {
        //query
        $query = "SELECT COUNT(*) FROM clients WHERE zone = '$zone' AND registered = 1";

        //prepare query
        $stmt = $this->conn->prepare($query);

        //execute query
        if ($stmt->execute())
        {
            return $stmt->fetchColumn();
        }

        return false;
    }
}
