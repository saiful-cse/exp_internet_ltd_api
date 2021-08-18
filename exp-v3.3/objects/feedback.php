<?php
include_once '../config/database.php';

class Feedback
{
    /*
     * Database connection
     */
    private $conn;

    /*
     * Object properties
     */
    public $id, $client_id, $feedback, $created_at;

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
        $query = "INSERT INTO feedback
                  SET client_id = :client_id, feedback = :feedback";

        //prepare query
        $stmt = $this->conn->prepare($query);

        //sanitize
        $this->client_id = htmlspecialchars($this->client_id);
        $this->feedback = htmlspecialchars($this->feedback);

        //Bind Value
        $stmt->bindParam(":client_id",$this->client_id);
        $stmt->bindParam(":feedback",$this->feedback);


        //Execute query
        if ($stmt->execute())
        {
            return true;
        }
        return false;
    }

    /*
     * Function for feedback load
     */

    function feedback_load()
    {
        $query = "SELECT * FROM feedback 
                  ORDER BY id 
                  DESC LIMIT 5";

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        //query execute
        $stmt->execute();
        return $stmt;

    }

    /*
     * Function for more feedback load
     */

    function more_feedback_load()
    {
        $query = "SELECT * FROM feedback 
                  WHERE id < '$this->id'
                  ORDER BY id 
                  DESC LIMIT 5";

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        //query execute
        $stmt->execute();
        return $stmt;

    }


}