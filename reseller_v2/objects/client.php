<?php
date_default_timezone_set("Asia/Dhaka");
include_once './config/database.php';

class Client
{
    /*
     * Database connection
     */
    public $conn, $zone, $first_date, $last_date;

    
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

    function transaction()
    {

        //query
        $query = "SELECT * FROM txn_list WHERE zone = :zone AND date >= :first_date AND date <= :last_date ORDER BY txn_id DESC";

        //prepare query
        $stmt = $this->conn->prepare($query);

        //Bind Value
        $stmt->bindParam(":zone", $this->zone);
        $stmt->bindParam(":first_date", $this->first_date);
        $stmt->bindParam(":last_date", $this->last_date);

        //query execute
        $stmt->execute();
        return $stmt;
    }
    
    function totalPayment()
    {
        $query = "SELECT SUM(credit) FROM txn_list WHERE zone = :zone AND date >= :first_date AND date <= :last_date";
        //prepare query
        $stmt = $this->conn->prepare($query);

        //Bind Value
        $stmt->bindParam(":zone", $this->zone);
        $stmt->bindParam(":first_date", $this->first_date);
        $stmt->bindParam(":last_date", $this->last_date);

        if ($stmt->execute()) {
            return $stmt->fetchColumn();
        }
        return false;
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
