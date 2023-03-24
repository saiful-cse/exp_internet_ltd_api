<?php
date_default_timezone_set("Asia/Dhaka");
include_once '../config/database.php';

class Txn
{
    /*
     * Database connection
     */
    private $conn;

    /*
     * Objects properties
     */
    public $details, $client_id, $expire_date, $name, $amount, $txnid;

    /*
     * Constructor with $db as database connection
     */
    public function __construct($db)
    {
        $this->conn = $db;
    }

    function bkash_txn_store()
    {
        try {

            $date =  date("Y-m-d H:i:s");
            
            $this->conn->beginTransaction();
            $query = "INSERT INTO txn_list 
              SET client_id = :client_id, name = :name, date = :date, credit = :credit, 
              type = 'Bill', details = :details, method = 'bKash', admin_id = '9588'";

            $query2 = "UPDATE clients SET mode = 'Enable', take_time = 0, expire_date = '$this->expire_date', sms = 'unsent'
               WHERE id = '$this->client_id'";

            //prepare query
            $stmt = $this->conn->prepare($query);
            $stmt2 = $this->conn->prepare($query2);

            //Bind Value
            $stmt->bindParam(":client_id", $this->client_id);
            $stmt->bindParam(":name", $this->name);
            $stmt->bindParam(":date", $date);
            $stmt->bindParam(":credit", $this->amount);
            $stmt->bindParam(":details", $this->details);
            
            $stmt->execute();
            $stmt2->execute();

            $this->conn->commit();
            return true;
        } catch (PDOException $e) {
            echo "Connection error: " . $e->getMessage();
            $this->conn->rollBack();
            return false;
        }
    }
}
