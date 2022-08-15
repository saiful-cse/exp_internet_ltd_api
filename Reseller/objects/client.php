<?php

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

    function client_list()
    {

        //query
        $query = "SELECT * FROM clients WHERE registered = 1 AND zone = 'Osman' ORDER BY id DESC LIMIT 10";

        //query execute
        $stmt = $this->conn->prepare($query);

        //query execute
        $stmt->execute();
        return $stmt;
        
    }
}
