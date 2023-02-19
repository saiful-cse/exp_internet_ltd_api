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
    public $client_id, $txn_id, $name, $date, $amount, $details, $first_date, $last_date,
        $type, $txn_type, $admin_id, $employee_id, $month, $method, $debit, $credit;

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
    function admin_make_payment()
    {

        if ($this->type == "Service") {

            $query = "INSERT INTO txn_list 
                  SET client_id = :client_id, name = :name, date = :date, credit = :credit, 
                  type = :type, details = :details, method = :method, admin_id = :admin_id";


            //prepare query
            $stmt = $this->conn->prepare($query);

            //Bind Value
            $stmt->bindParam(":client_id", $this->client_id);
            $stmt->bindParam(":name", $this->name);
            $stmt->bindParam(":date", $this->date);
            $stmt->bindParam(":credit", $this->amount);
            $stmt->bindParam(":type", $this->type);
            $stmt->bindParam(":details", $this->details);
            $stmt->bindParam(":method", $this->method);
            $stmt->bindParam(":admin_id", $this->admin_id);

            //Execute query
            if ($stmt->execute()) {
                return true;
            }
            return false;
        } else if ($this->type == "Bill") {

            try {

                $this->conn->beginTransaction();

                $query = "INSERT INTO txn_list 
                  SET client_id = :client_id, name = :name, date = :date, credit = :credit, 
                  type = :type, details = :details, method = :method, admin_id = :admin_id";

                $query2 = "UPDATE clients SET expire_date = DATE_ADD(expire_date, INTERVAL 1 MONTH), sms = 'unsent'
                   WHERE id = '$this->client_id'";

                //prepare query
                $stmt = $this->conn->prepare($query);
                $stmt2 = $this->conn->prepare($query2);

                //Bind Value
                $stmt->bindParam(":client_id", $this->client_id);
                $stmt->bindParam(":name", $this->name);
                $stmt->bindParam(":date", $this->date);
                $stmt->bindParam(":credit", $this->amount);
                $stmt->bindParam(":type", $this->type);
                $stmt->bindParam(":details", $this->details);
                $stmt->bindParam(":method", $this->method);
                $stmt->bindParam(":admin_id", $this->admin_id);

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

    /*
     * Make admin transaction
     */
    function admin_make_txn($txn_type)
    {

        if ($txn_type == "Credit") {
            $query = "INSERT INTO txn_list 
                  SET 
                  date = :date, credit = :credit, details = :details, method = :method, admin_id = :admin_id";

            //prepare query
            $stmt = $this->conn->prepare($query);

            //Bind Value
            $stmt->bindParam(":date", $this->date);
            $stmt->bindParam(":credit", $this->amount);
            $stmt->bindParam(":details", $this->details);
            $stmt->bindParam(":method", $this->method);
            $stmt->bindParam(":admin_id", $this->admin_id);

            //Execute query
            if ($stmt->execute()) {
                return true;
            }
            return false;

        } else if ($txn_type == "Debit") {
            $query = "INSERT INTO txn_list 
                  SET date = :date, debit = :debit, details = :details, method = :method, admin_id = :admin_id";

            //prepare query
            $stmt = $this->conn->prepare($query);

            //Bind Value
            $stmt->bindParam(":date", $this->date);
            $stmt->bindParam(":debit", $this->amount);
            $stmt->bindParam(":details", $this->details);
            $stmt->bindParam(":method", $this->method);
            $stmt->bindParam(":admin_id", $this->admin_id);

            //Execute query
            if ($stmt->execute()) {
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
        $query = "SELECT txn_id, date, credit, details FROM txn_list WHERE client_id = '$this->id' ORDER BY date DESC";

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
        $stmt->bindParam(":first_date", $this->first_date);
        $stmt->bindParam(":last_date", $this->last_date);

        //query execute
        $stmt->execute();
        return $stmt;
    }

    function salay_list()
    {

        $query = "SELECT * FROM salary_list WHERE employee_id = :employee_id ORDER BY date DESC";

        //prepare query
        $stmt = $this->conn->prepare($query);

        //Bind Value
        $stmt->bindParam(":employee_id", $this->employee_id);

        //query execute
        $stmt->execute();
        return $stmt;
    }

    function add_salary()
    {

        try {
            $this->conn->beginTransaction();
            $query1 = "INSERT INTO salary_list 
                  SET employee_id = :employee_id, month = :month, amount = :amount, date = :date";

            $query2 = "INSERT INTO txn_list 
                  SET date = :date, debit = :debit, type = 'Salary', details = :details, method = 'Cash', admin_id = :admin_id";

            //prepare query
            $stmt1 = $this->conn->prepare($query1);
            $stmt2 = $this->conn->prepare($query2);

            //Bind Value for query 1
            $stmt1->bindParam(":employee_id", $this->employee_id);
            $stmt1->bindParam(":month", $this->month);
            $stmt1->bindParam(":amount", $this->amount);
            $stmt1->bindParam(":date", $this->date);


            //Bind Value for query 2
            $stmt2->bindParam(":date", $this->date);
            $stmt2->bindParam(":debit", $this->amount);
            $stmt2->bindParam(":details", $this->details);
            $stmt2->bindParam(":admin_id", $this->admin_id);

            $stmt1->execute();
            $stmt2->execute();

            $this->conn->commit();
            return true;


        } catch (\Throwable $th) {
            echo "Connection error: " . $th->getMessage();
            $this->conn->rollBack();
            return false;
        }
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
        $stmt->bindParam(":first_date", $this->first_date);
        $stmt->bindParam(":last_date", $this->last_date);

        //execute query
        if ($stmt->execute()) {
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
        $stmt->bindParam(":first_date", $this->first_date);
        $stmt->bindParam(":last_date", $this->last_date);

        //execute query
        if ($stmt->execute()) {
            return $stmt->fetchColumn();
        }
        return false;
    }

    function txn_delete()
    {
        $query = "DELETE FROM txn_list WHERE txn_id = '$this->txn_id'";
        //prepare query
        $stmt = $this->conn->prepare($query);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    function txn_update()
    {
        $query = "UPDATE txn_list
                    SET date = :date, admin_id = :admin_id, details = :details, credit = :credit, debit = :debit
                    WHERE txn_id = :txn_id";
        //prepare query
        $stmt = $this->conn->prepare($query);

        //Bind Value
        $stmt->bindParam(":txn_id", $this->txn_id);
        $stmt->bindParam(":date", $this->date);
        $stmt->bindParam(":admin_id", $this->admin_id);
        $stmt->bindParam(":details", $this->details);
        $stmt->bindParam(":credit", $this->credit);
        $stmt->bindParam(":debit", $this->debit);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }


    function datewise_and_adminwise_total_credit_debit()
    {
        //query
        $query = "SELECT admin_id, SUM(credit) - SUM(debit) as cash 
        FROM txn_list WHERE date >= :first_date AND date <= :last_date
        GROUP BY admin_id";

        //prepare query
        $stmt = $this->conn->prepare($query);

        //Bind Value
        $stmt->bindParam(":first_date", $this->first_date);
        $stmt->bindParam(":last_date", $this->last_date);

        $stmt->execute();
        return $stmt;
    }
}
