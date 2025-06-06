<?php
date_default_timezone_set("Asia/Dhaka");

class Dashboard
{
    /*
     * Database connection
     */
    private $conn;

    /*
     * Objects properties
     */
    public $total_active, $total_inactive, $month_total_credit, $month_total_debit, $overall_credit, $overall_debit;

    /*
     * Constructor with $db as database connection
     */
    public function __construct($db)
    {
        $this->conn = $db;
    }

    /*
     * Function for counting total active client
     */
    function count_total_active_client()
    {
        //query
        $query = "SELECT COUNT(*) FROM client WHERE mode = 'active'";

        //prepare query
        $stmt = $this->conn->prepare($query);

        //execute query
        if ($stmt->execute())
        {
            return $stmt->fetchColumn();
        }

        return false;
    }

    /*
     * Function for counting total inactive client
     */
    function count_total_inactive_client()
    {
        //query
        $query = "SELECT COUNT(*) FROM client WHERE mode = 'inactive'";

        //prepare query
        $stmt = $this->conn->prepare($query);

        //execute query
        if ($stmt->execute())
        {
            return $stmt->fetchColumn();
        }

        return false;
    }

    /*
     * Function for counting total current month credit
     */
    function current_month_total_credit()
    {
        //getting current timestamp
        $current_date = date("Y-m-d H:i:s");

        //query
        $query = "SELECT SUM(credit) FROM txn_list WHERE MONTH(date) = MONTH('$current_date') 
        AND YEAR(date) = YEAR('$current_date')";

        //prepare query
        $stmt = $this->conn->prepare($query);

        //execute query
        if ($stmt->execute())
        {
            return $stmt->fetchColumn();
        }
        return false;
    }

    /*
     * Function for counting total current month debit
     */
    function current_month_total_debit()
    {
        //getting current timestamp
        $current_date = date("Y-m-d H:i:s");

        //query
        $query = "SELECT SUM(debit) FROM txn_list WHERE MONTH(date) = MONTH('$current_date') 
        AND YEAR(date) = YEAR('$current_date')";

        //prepare query
        $stmt = $this->conn->prepare($query);

        //execute query
        if ($stmt->execute())
        {
            return $stmt->fetchColumn();
        }
        return false;
    }

    /*
     * Function for counting total overall credit
     */
    function overall_credit()
    {
        //query
        $query = "SELECT FORMAT(SUM(credit),2) credit FROM txn_list";

        //prepare query
        $stmt = $this->conn->prepare($query);

        //execute query
        if ($stmt->execute())
        {
            return $stmt->fetchColumn();
        }
        return false;
    }

    /*
     * Function for counting total overall debit
     */
    function overall_debit()
    {
        //query
        $query = "SELECT FORMAT(SUM(debit),2) debit FROM txn_list";

        //prepare query
        $stmt = $this->conn->prepare($query);

        //execute query
        if ($stmt->execute())
        {
            return $stmt->fetchColumn();
        }
        return false;
    }


function count_monthly_client(){

        $query = "SELECT MONTHNAME(reg_date) AS month , COUNT(*) AS total FROM client
        WHERE YEAR(reg_date) = 2021 AND MODE = 'Active'
        GROUP BY MONTH(reg_date)
        ORDER BY MONTH(reg_date) DESC LIMIT 8";

        // prepare query
        $stmt = $this->conn->prepare($query);
        
        // execute query
        $stmt->execute();
        return $stmt;

    }
    
    function total_invest()
    {
    
        //query
        $query = "SELECT SUM(misba) as misba, SUM(saiful) as saiful FROM invests";

        //prepare query
        $stmt = $this->conn->prepare($query);

        $stmt->execute();
        return $stmt;
    }


}