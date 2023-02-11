<?php
date_default_timezone_set("Asia/Dhaka");
include_once '../config/database.php';

class Sms
{
    /*
     * Database connection
     */
    private $conn;

    /*
     * Objects properties
     */
    public  $numbers, $msg_id, $client_id, $msg_body, 
    $area, $created_at, $current_date, $id_list;

     /*
     * Constructor with $db as database connection
     */
    public function __construct($db)
    {
        $this->conn = $db;
    }

    function getExpiredClientsPhonePPPname()
    {
        $current_date =  date("Y-m-d H:i:s");

        $query = "SELECT id, ppp_name, phone FROM clients WHERE '$current_date' >= expire_date AND mode = 'Enable' AND payment_method = 'Mobile' AND take_time = 0";
        $stmt = $this->conn->prepare($query);
        //query execute
        $stmt->execute();
        return $stmt;
    }

    function clientDisconnectModeUpdate()
    
    {
        $current_date =  date("Y-m-d H:i:s");
        $query = "UPDATE clients SET mode = 'Disable', sms = 'unsent', take_time = 0, disable_date = '$current_date' WHERE id IN ($this->id_list) ";
        // prepare query statement
        $stmt = $this->conn->prepare($query);

        //query execute
        if ($stmt->execute()) {

            return true;
        }
        return false;
    }

    function getEnabledClientsPhone(){
        //query
        $query = "SELECT phone FROM clients WHERE mode = 'Enable' AND registered = 1";

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        //query execute
        $stmt->execute();
        return $stmt;
    }
 
    function getExpiredClientsPhone()
    {

        $current_date =  date('Y-m-d H:i:s', strtotime(date("Y-m-d H:i:s"). '+3 days')); 
        $query = "SELECT phone FROM clients WHERE expire_date <= '$current_date' AND sms = 'unsent' AND take_time = 0 AND registered = 1 AND mode = 'Enable' ";
        $stmt = $this->conn->prepare($query);
        //query execute
        $stmt->execute();
        return $stmt;
    }


    //enabled clients sms store
    function expiredClientSmsStoreUpdate(){
        
        try {

            $this->conn->beginTransaction();

            //query
            $query1 = "UPDATE clients SET sms = 'sent'
            WHERE phone IN ($this->numbers)";

            $query2 = "INSERT INTO messages 
            SET msg_body = :msg_body, tag = 'warning', created_at = :created_at";

            //prepare query
            $stmt1 = $this->conn->prepare($query1);
            $stmt2 = $this->conn->prepare($query2);

            //Bind value
            $stmt2->bindParam(":msg_body",$this->msg_body);
            $stmt2->bindParam(":created_at",$this->created_at);
            
            $stmt1->execute();
            $stmt2->execute();

            $this->conn->commit();
            return true;

        } catch (\Throwable $e) {
            echo "Connection error: ".$e->getMessage();
            $this->conn->rollBack();
            return false;
        }
    }

    //active client sms store
    function enabledClientSmsStore(){
        //query
        $query = "INSERT INTO messages 
        SET msg_body = :msg_body, tag = 'all', created_at = :created_at";

        //prepare query
        $stmt = $this->conn->prepare($query);

        //Bind value
        $stmt->bindParam(":msg_body",$this->msg_body);
        $stmt->bindParam(":created_at",$this->created_at);

        //execute query
        if ($stmt->execute())
        {
            return true;
        }

        return false;
    }

    //id wise sms store
    function idwise_sms_store(){
        //query
        $query = "INSERT INTO messages 
        SET msg_body = :msg_body, client_id = :client_id, created_at = :created_at";

        //prepare query
        $stmt = $this->conn->prepare($query);

        //Bind value
        $stmt->bindParam(":msg_body",$this->msg_body);
        $stmt->bindParam(":client_id",$this->client_id);
        $stmt->bindParam(":created_at",$this->created_at);

        //execute query
        if ($stmt->execute())
        {
            return true;
        }

        return false;
    }

    function areawise_sms_store(){
        //query
        $query = "INSERT INTO messages 
        SET msg_body = :msg_body, tag = :tag , created_at = :created_at";

        //prepare query
        $stmt = $this->conn->prepare($query);

        //Bind value
        $stmt->bindParam(":msg_body",$this->msg_body);
        $stmt->bindParam(":tag",$this->tag);
        $stmt->bindParam(":created_at",$this->created_at);

        //execute query
        if ($stmt->execute())
        {
            return true;
        }

        return false;
    }

    //get client sms history
    function sms_history(){
        //query
        $query = "SELECT * FROM messages
        ORDER BY created_at DESC LIMIT 20";

        // prepare query statement
        $stmt = $this->conn->prepare($query);
        
        //query execute
        $stmt->execute();
        return $stmt;
    }
    
    //get client sms history
    function more_sms_history(){
        //query
        $query = "SELECT * FROM messages WHERE msg_id < '$this->last_id'
        ORDER BY created_at DESC LIMIT 20";

        // prepare query statement
        $stmt = $this->conn->prepare($query);
        
        //query execute
        $stmt->execute();
        return $stmt;
    }

    //get client sms history
    function getting_areawise_client_phone(){
        //query
        $query = "SELECT phone FROM clients WHERE mode = 'Enable' AND registered = 1 AND area = :area";

        // prepare query statement
        $stmt = $this->conn->prepare($query);
        
        //Bind the value
        $stmt->bindParam(":area",$this->area);

        //query execute
        $stmt->execute();
        return $stmt;
    }

}

?>