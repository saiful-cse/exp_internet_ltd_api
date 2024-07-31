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
    public $total_active, $zone, $total_inactive, $month_total_credit, $month_total_debit, $overall_credit, $overall_debit;

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
    function count_total_enabled_client()
    {

        //query
        $query = "";
        if ($this->zone === 'All') {
            $query = "SELECT COUNT(*) FROM clients WHERE mode = 'Enable'";
        } else {
            $query = "SELECT COUNT(*) FROM clients WHERE mode = 'Enable' AND zone = '$this->zone'";
        }

        //prepare query
        $stmt = $this->conn->prepare($query);

        //execute query
        if ($stmt->execute()) {
            return $stmt->fetchColumn();
        }

        return false;
    }

    /*
     * Function for counting total inactive client
     */
    function count_total_disabled_client()
    {
        //query
        $query = "";
        if ($this->zone === 'All') {
            $query = "SELECT COUNT(*) FROM clients WHERE mode = 'Disable' AND registered = 1";
        } else {
            $query = "SELECT COUNT(*) FROM clients WHERE mode = 'Disable' AND registered = 1 AND zone = '$this->zone'";
        }

        //prepare query
        $stmt = $this->conn->prepare($query);

        //execute query
        if ($stmt->execute()) {
            return $stmt->fetchColumn();
        }

        return false;
    }

    function count_total_expired_client()
    {
        $current_date = date("Y-m-d H:i:s");
        $query = "";
        if ($this->zone === 'All') {
            $query = "SELECT COUNT(*) FROM clients WHERE date(expire_date) <= '$current_date' AND registered = 1 AND mode = 'Enable'";
        } else {
            $query = "SELECT COUNT(*) FROM clients WHERE date(expire_date) <= '$current_date' AND registered = 1 AND mode = 'Enable' AND zone = '$this->zone'";
        }

        //prepare query
        $stmt = $this->conn->prepare($query);

        //execute query
        if ($stmt->execute()) {
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

        $query = "";
        if ($this->zone === 'All') {
            $query = "SELECT SUM(credit) FROM txn_list WHERE MONTH(date) = MONTH('$current_date') 
            AND YEAR(date) = YEAR('$current_date')";
        } else {
            $query = "SELECT SUM(credit) FROM txn_list WHERE MONTH(date) = MONTH('$current_date') 
            AND YEAR(date) = YEAR('$current_date') AND zone = '$this->zone'";
        }

        //prepare query
        $stmt = $this->conn->prepare($query);

        //execute query
        if ($stmt->execute()) {
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

        $query = "";
        if ($this->zone === 'All') {
            $query = "SELECT SUM(debit) FROM txn_list WHERE MONTH(date) = MONTH('$current_date') 
            AND YEAR(date) = YEAR('$current_date')";
        } else {
            $query = "SELECT SUM(debit) FROM txn_list WHERE MONTH(date) = MONTH('$current_date') 
            AND YEAR(date) = YEAR('$current_date') AND zone = '$this->zone'";
        }
        //query

        //prepare query
        $stmt = $this->conn->prepare($query);

        //execute query
        if ($stmt->execute()) {
            return $stmt->fetchColumn();
        }
        return false;
    }

    function current_month_total_service()
    {
        //getting current timestamp
        $current_date = date("Y-m-d H:i:s");

        $query = "";
        if ($this->zone === 'All') {
            $query = "SELECT SUM(credit) FROM txn_list WHERE type = 'Service' AND MONTH(date) = MONTH('$current_date') AND 
            YEAR(date) = YEAR('$current_date')";
        } else {
            $query = "SELECT SUM(credit) FROM txn_list WHERE type = 'Service' AND MONTH(date) = MONTH('$current_date') AND 
            YEAR(date) = YEAR('$current_date') AND zone = '$this->zone'";
        }
        //query

        //prepare query
        $stmt = $this->conn->prepare($query);

        //execute query
        if ($stmt->execute()) {
            return $stmt->fetchColumn();
        }
        return false;
    }

    function current_month_total_bill()
    {
        //getting current timestamp
        $current_date = date("Y-m-d H:i:s");

        $query = "";
        if ($this->zone === 'All') {
            $query = "SELECT SUM(credit) FROM txn_list WHERE type = 'Bill' AND MONTH(date) = MONTH('$current_date') AND 
            YEAR(date) = YEAR('$current_date') ";
        }else{
            $query = "SELECT SUM(credit) FROM txn_list WHERE type = 'Bill' AND MONTH(date) = MONTH('$current_date') AND 
            YEAR(date) = YEAR('$current_date') AND zone = '$this->zone'";
        }
        //query
       

        //prepare query
        $stmt = $this->conn->prepare($query);

        //execute query
        if ($stmt->execute()) {
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
        if ($stmt->execute()) {
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
        if ($stmt->execute()) {
            return $stmt->fetchColumn();
        }
        return false;
    }


    function package_usage()
    {
        $query = "";
        if ($this->zone === 'All') {
            $query = "SELECT pkg_id as pkg_name, COUNT(*) AS pkg_qtn FROM clients
            WHERE mode = 'Enable' AND registered = '1'
            GROUP BY pkg_id
            ORDER BY pkg_id";
        } else {
            $query = "SELECT pkg_id as pkg_name, COUNT(*) AS pkg_qtn FROM clients
            WHERE zone = '$this->zone' AND  mode = 'Enable' AND registered = '1'
            GROUP BY pkg_id
            ORDER BY pkg_id";
        }

        // prepare query
        $stmt = $this->conn->prepare($query);

        // execute query
        $stmt->execute();
        return $stmt;
    }


    function count_monthly_client()
    {

        $query = "";
        $current_date = date("Y-m-d H:i:s");
        if ($this->zone === 'All') {
            $query = "SELECT MONTHNAME(reg_date) AS month , COUNT(*) AS total FROM clients
            WHERE YEAR(reg_date) = YEAR('$current_date') AND mode = 'Enable' AND registered = '1'
            GROUP BY MONTH(reg_date)
            ORDER BY MONTH(reg_date) DESC";
        } else {
            $query = "SELECT MONTHNAME(reg_date) AS month , COUNT(*) AS total FROM clients
            WHERE YEAR(reg_date) = YEAR('$current_date') AND mode = 'Enable' AND registered = '1' AND zone = '$this->zone'
            GROUP BY MONTH(reg_date)
            ORDER BY MONTH(reg_date) DESC";
        }


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



    function expired_cash()
    {

        $current_date = date("Y-m-d H:i:s");
        //query
        $query = "SELECT COUNT(*) FROM clients WHERE date(expire_date) <= '$current_date' AND registered = 1 AND mode = 'Enable' AND payment_method = 'Cash'";

        //prepare query
        $stmt = $this->conn->prepare($query);

        if ($stmt->execute()) {
            return $stmt->fetchColumn();
        }
        return false;
    }

    function expired_mobile()
    {

        $current_date = date("Y-m-d H:i:s");
        //query
        $query = "SELECT COUNT(*) FROM clients WHERE date(expire_date) <= '$current_date' AND registered = 1 AND mode = 'Enable' AND payment_method = 'Mobile'";

        //prepare query
        $stmt = $this->conn->prepare($query);

        if ($stmt->execute()) {
            return $stmt->fetchColumn();
        }
        return false;
    }

    function bKashCollection()
    {

        $current_date = date("Y-m-d H:i:s");
        $query = "SELECT MONTHNAME(date) AS label, SUM(credit) AS y FROM txn_list
        WHERE '2024-01-01 00:00:00' <= date AND '$current_date' >= date AND type = 'Bill' AND method = 'bKash'
        GROUP BY MONTH(date)
        ORDER BY MONTH(date)";

        //prepare query
        $stmt = $this->conn->prepare($query);

        $stmt->execute();
        return $stmt;
    }

    function packages()
    {
        $query = "SELECT * FROM packages";
        $stmt = $this->conn->prepare($query);

        $stmt->execute();
        return $stmt;
    }
}
