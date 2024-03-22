<?php
include_once '../config/database.php';

class Area
{
    /*
     * Database connection
     */
    private $conn;

    /*
     * Objects properties
     */
    public $id, $area_name;
    /*
     * Constructor with $db as database connection
     */
    public function __construct($db)
    {
        $this->conn = $db;
    }

    function area_list()
    {
        //query
        $query = "SELECT * FROM areas";

        // prepare query statement
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
}
