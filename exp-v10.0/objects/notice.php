<?php
include_once '../config/database.php';

class Notice
{
    /*
     * Database connection
     */
    private $conn;

    /*
     * Object properties
     */
    public $id, $notice, $sms;

    /*
     * Constructor with $db as database connection
     */
    public function __construct($db)
    {
        $this->conn = $db;
    }

    /*
     * Function for feedback create
     */

    function create()
    {
        $query = "INSERT INTO notice
                  SET notice = :notice";

        //prepare query
        $stmt = $this->conn->prepare($query);

        //sanitize
        $this->notice = htmlspecialchars($this->notice);

        //Bind Value
        $stmt->bindParam(":notice",$this->notice);

        //Execute query
        if ($stmt->execute())
        {
            return true;
        }
        return false;
    }

    /*
     * Function for notice load
     */

    function notice_load()
    {
        $query = "SELECT * FROM notice 
                  ORDER BY id 
                  DESC LIMIT 5";

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        //query execute
        $stmt->execute();
        return $stmt;

    }

    /*
     * Function for more notice load
     */

    function more_notice_load()
    {
        $query = "SELECT * FROM notice 
                  WHERE id < '$this->id'
                  ORDER BY id 
                  DESC LIMIT 5";

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        //query execute
        $stmt->execute();
        return $stmt;

    }

    //delete notice
    function delete(){

        // select all query
        $query = "DELETE FROM notice WHERE id = :id";

        // prepare query statement
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);

        // execute query
        if($stmt->execute()){
            return true;
        }
        return false;
    }

    function update()
    {
        //query
        $query = "UPDATE notice 
                  SET notice = :notice
                  WHERE id = '$this->id'";

        //prepare query
        $stmt = $this->conn->prepare($query);

        //sanitize
        $this->notice = htmlspecialchars($this->notice);

        //Bind Value
        $stmt->bindParam(":notice",$this->notice);

        //Execute query
        if ($stmt->execute())
        {
            return true;
        }
        return false;

    }


}