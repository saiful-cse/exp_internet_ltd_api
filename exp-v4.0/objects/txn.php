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
    public $id, $txn_id, $name, $date, $amount, $details, $first_date, $last_date, $payment_type,  $type, $debit, $credit;

    /*
     * Constructor with $db as database connection
     */
    public function __construct($db)
    {
        $this->conn = $db;
    }

    /*
     * Make client transaction
     */
    function client_txn()
    {

        if ($this->payment_type == "Service")
        {

            $query = "INSERT INTO txn_list 
                  SET client_id = :client_id, name = :name, date = :date, credit = :credit, type = :type, details = :details";


            //prepare query
            $stmt = $this->conn->prepare($query);

            //Bind Value
            $stmt->bindParam(":client_id",$this->id);
            $stmt->bindParam(":name",$this->name);
            $stmt->bindParam(":date",$this->date);
            $stmt->bindParam(":credit",$this->amount);
            $stmt->bindParam(":type",$this->payment_type);
            $stmt->bindParam(":details",$this->details);

            //Execute query
            if ($stmt->execute())
            {
                return true;
            }
            return false;


        }else if($this->payment_type == "Bill")
        {
            
            try {

                $this->conn->beginTransaction();

                $query = "INSERT INTO txn_list 
                  SET client_id = :client_id, name = :name, date = :date, credit = :credit, type = :type, details = :details";

                $query2 = "UPDATE client SET active_date = DATE_ADD(active_date, INTERVAL 1 MONTH), sms = 'unsent'
                   WHERE id = '$this->id'";

                //prepare query
                $stmt = $this->conn->prepare($query);
                $stmt2 = $this->conn->prepare($query2);

                //Bind Value
                $stmt->bindParam(":client_id",$this->id);
                $stmt->bindParam(":name",$this->name);
                $stmt->bindParam(":date",$this->date);
                $stmt->bindParam(":credit",$this->amount);
                $stmt->bindParam(":type",$this->payment_type);
                $stmt->bindParam(":details",$this->details);

                $stmt->execute();
                $stmt2->execute();

                $this->conn->commit();
                return true;

            } catch (PDOException $e) {
                echo "Connection error: ".$e->getMessage();
                $this->conn->rollBack();
                return false;
            }
            
            /*
            // Old code
            
            $query = "INSERT INTO txn_list 
                  SET client_id = :client_id, name = :name, date = :date, credit = :credit, type = :type, details = :details";

            $query2 = "UPDATE client SET active_date = DATE_ADD(active_date, INTERVAL 1 MONTH) , alert = 0
                  WHERE id = '$this->id'";

            //prepare query
            $stmt = $this->conn->prepare($query);
            $stmt2 = $this->conn->prepare($query2);

            //Bind Value
            $stmt->bindParam(":client_id",$this->id);
            $stmt->bindParam(":name",$this->name);
            $stmt->bindParam(":date",$this->date);
            $stmt->bindParam(":credit",$this->amount);
            $stmt->bindParam(":type",$this->payment_type);
            $stmt->bindParam(":details",$this->details);

            //Execute query
            if ($stmt->execute() && $stmt2->execute())
            {
                return true;
            }
            return false;
            */
        }


    }

    /*
     * Make admin transaction
     */
    function admin_txn($txn_type)
    {

        if ($txn_type == "Credit")
        {
            $query = "INSERT INTO txn_list 
                  SET date = :date, credit = :credit, details = :details";

            //prepare query
            $stmt = $this->conn->prepare($query);

            //Bind Value
            $stmt->bindParam(":date",$this->date);
            $stmt->bindParam(":credit",$this->amount);
            $stmt->bindParam(":details",$this->details);

            //Execute query
            if ($stmt->execute())
            {
                return true;
            }
            return false;

        }else if($txn_type == "Debit")
        {
            $query = "INSERT INTO txn_list 
                  SET date = :date, debit = :debit, details = :details";

            //prepare query
            $stmt = $this->conn->prepare($query);

            //Bind Value
            $stmt->bindParam(":date",$this->date);
            $stmt->bindParam(":debit",$this->amount);
            $stmt->bindParam(":details",$this->details);

            //Execute query
            if ($stmt->execute())
            {
                return true;
            }
            return false;
        }


    }

    /*
     * client transaction details
     */
    function payment_details()
    {
        $query = "SELECT txn_id, date, credit, details FROM txn_list WHERE client_id = '$this->id' ORDER BY date DESC LIMIT 5";

        //prepare query
        $stmt = $this->conn->prepare($query);

        //query execute
        $stmt->execute();
        return $stmt;
    }

    /*
     * client transaction details
     */
    function all_txn()
    {

        
        $query = "SELECT * FROM txn_list WHERE date >= :first_date AND date <= :last_date ORDER BY date ASC";

        //prepare query
        $stmt = $this->conn->prepare($query);

        //Bind Value
        $stmt->bindParam(":first_date",$this->first_date);
        $stmt->bindParam(":last_date",$this->last_date);

        //query execute
        $stmt->execute();
        return $stmt;
    }
    
    /*
     * Function for txn details
     */
    function txn_details()
    {

        //query
        $query = "SELECT * FROM txn_list WHERE txn_id = '$this->txn_id'";

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        //query execute
        $stmt->execute();
        return $stmt;
    }

    function datewise_total_credit()
    {
        
        //query
        $query = "SELECT FORMAT(SUM(credit),2) credit FROM txn_list WHERE date >= :first_date AND date <= :last_date";

        //prepare query
        $stmt = $this->conn->prepare($query);

        //Bind Value
        $stmt->bindParam(":first_date",$this->first_date);
        $stmt->bindParam(":last_date",$this->last_date);

        //execute query
        if ($stmt->execute())
        {
            return $stmt->fetchColumn();
        }
        return false;
    }

    function datewise_total_debit()
    {
        //query
        $query = "SELECT FORMAT(SUM(debit),2) debit FROM txn_list WHERE date >= :first_date AND date <= :last_date";

        //prepare query
        $stmt = $this->conn->prepare($query);

        //Bind Value
        $stmt->bindParam(":first_date",$this->first_date);
        $stmt->bindParam(":last_date",$this->last_date);

        //execute query
        if ($stmt->execute())
        {
            return $stmt->fetchColumn();
        }
        return false;
    }
    
    function txn_delete()
    {
        $query = "DELETE FROM txn_list WHERE txn_id = '$this->txn_id'";
        //prepare query
        $stmt = $this->conn->prepare($query);

        if($stmt->execute())
        {
            return true;
        }
        return false;
    }
    
    function txn_update()
    {
        $query = "UPDATE txn_list
                    SET date = :date, details = :details, credit = :credit, debit = :debit
                    WHERE txn_id = :txn_id";
        //prepare query
        $stmt = $this->conn->prepare($query);

        //Bind Value
        $stmt->bindParam(":txn_id",$this->txn_id);
        $stmt->bindParam(":date",$this->date);
        $stmt->bindParam(":details",$this->details);
        $stmt->bindParam(":credit",$this->credit);
        $stmt->bindParam(":debit",$this->debit);

        if($stmt->execute())
        {
            return true;
        }
        return false;
    }



}