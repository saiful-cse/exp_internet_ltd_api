<?php
date_default_timezone_set("Asia/Dhaka");
include_once '../config/database.php';

class Client
{
    /*
     * Database connection
     */
    private $conn;

    /*
     * Objects properties
     */
    public $id, $registered, $mode, $payment_method, $name, $phone, $area, $zone,
        $ppp_name, $ppp_pass,
        $pkg_id, $sms,
        $reg_date, $expire_date, $ppp_name_list;

    public $search_key;
    /*
     * Constructor with $db as database connection
     */
    public function __construct($db)
    {
        $this->conn = $db;
    }


    // ------------------ Active --------------------
    function registered_client()
    {
        //query
        $query = "SELECT * FROM clients WHERE registered = 1 ORDER BY reg_date DESC";

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        //query execute
        $stmt->execute();
        return $stmt;
    }

    function unregistered_client()
    {
        //query
        $query = "SELECT * FROM clients WHERE registered = 0 ORDER BY reg_date DESC";

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        //query execute
        $stmt->execute();
        return $stmt;
    }


    function enabled_client()
    {

        //query
        $query = "SELECT * FROM clients WHERE registered = '1' AND mode = 'Enable' ORDER BY reg_date DESC";

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        //query execute
        $stmt->execute();
        return $stmt;
    }

    function diabled_client()
    {

        //query
        $query = "SELECT * FROM clients WHERE registered = '1' AND mode = 'Disable' ORDER BY reg_date DESC";

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        //query execute
        $stmt->execute();
        return $stmt;
    }



    /*
     * Function for client details
     */
    function client_details()
    {

        //query
        $query = "SELECT * FROM clients WHERE ppp_name = '$this->ppp_name'";

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        //query execute
        $stmt->execute();
        return $stmt;
    }

    function client_details_id()
    {

        //query
        $query = "SELECT * FROM clients WHERE id = '$this->id'";

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        //query execute
        $stmt->execute();
        return $stmt;
    }

    function expired_client()
    {

        //query
        $query = "SELECT * FROM clients WHERE expire_date <= '$this->current_date' AND registered = '1' AND mode = 'Enable' ORDER BY reg_date DESC";

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        //query execute
        $stmt->execute();
        return $stmt;
    }

    


    function isExistPhoneToUpdate()
    {

        $query = "SELECT phone FROM clients WHERE phone = :phone && id != $this->id";
        // prepare the query
        $stmt = $this->conn->prepare($query);

        // sanitize
        $stmt->bindParam(":phone", $this->phone);

        // execute the query
        $stmt->execute();
        // get number of rows
        $num = $stmt->rowCount();

        if ($num > 0) {

            return true;
        }
        return false;
    }

    function isExistPhone()
    {
        $query = "SELECT phone FROM clients WHERE phone = :phone";
        // prepare the query
        $stmt = $this->conn->prepare($query);

        // sanitize
        $stmt->bindParam(":phone", $this->phone);

        // execute the query
        $stmt->execute();
        // get number of rows
        $num = $stmt->rowCount();

        if ($num > 0) {

            return true;
        }
        return false;
    }

    function isExistPPPname()
    {
        $query = "SELECT id, ppp_name FROM clients WHERE ppp_name = :ppp_name && id != $this->id";

        // prepare the query
        $stmt = $this->conn->prepare($query);

        // sanitize
        $stmt->bindParam(":ppp_name", $this->ppp_name);

        // execute the query
        $stmt->execute();
        // get number of rows
        $num = $stmt->rowCount();

        if ($num > 0) {

            return true;
        }
        return false;
    }

    function all_packages()
    {

        //query
        $query = "SELECT * FROM packages";

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        //query execute
        $stmt->execute();
        return $stmt;
    }

    function allClient()
    {

        //query
        $query = "SELECT * FROM clients";

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        //query execute
        $stmt->execute();
        return $stmt;
    }

    function modeUpdate()
    {
        $query = "UPDATE clients SET
        mode = :mode WHERE id = $this->id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":mode", $this->mode);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }


    function clientRegistration()
    {

        $current_date =  date("Y-m-d H:i:s");
        $query = "INSERT INTO clients 
        SET registered = '0', name = :name, phone = :phone, area = :area, reg_date = :reg_date";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":phone", $this->phone);
        $stmt->bindParam(":area", $this->area);
        $stmt->bindParam(":reg_date", $current_date);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    function client_details_update()
    {


        $query1 = "SELECT mode FROM clients WHERE id = $this->id";

        $stmt = $this->conn->query($query1);
        $f = $stmt->fetch();
        $current_mode = $f['mode'];

        if ($current_mode == $this->mode) {
            $query = "UPDATE clients SET
            registered = '1', mode = :mode, payment_method = :payment_method, name = :name, phone = :phone, area = :area,
            zone = :zone, expire_date = :expire_date, ppp_name = :ppp_name, ppp_pass = :ppp_pass, pkg_id = :pkg_id WHERE id = $this->id";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(":mode", $this->mode);
            $stmt->bindParam(":payment_method", $this->payment_method);
            $stmt->bindParam(":name", $this->name);
            $stmt->bindParam(":phone", $this->phone);
            $stmt->bindParam(":area", $this->area);
            $stmt->bindParam(":zone", $this->zone);
            $stmt->bindParam(":expire_date", $this->expire_date);
            $stmt->bindParam(":ppp_name", $this->ppp_name);
            $stmt->bindParam(":ppp_pass", $this->ppp_pass);
            $stmt->bindParam(":pkg_id", $this->pkg_id);

            if ($stmt->execute()) {
                return true;
            }
            return false;

        } else if ($current_mode == 'Disable' && $this->mode == 'Enable') {
            
            $current_date = date("Y-m-d H:i:s");
            
            $query = "UPDATE clients SET
            registered = '1', mode = :mode, payment_method = :payment_method, name = :name, phone = :phone, area = :area,
            zone = :zone, expire_date = :expire_date, ppp_name = :ppp_name, ppp_pass = :ppp_pass, pkg_id = :pkg_id, expire_date = :expire_date WHERE id = $this->id";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(":mode", $this->mode);
            $stmt->bindParam(":payment_method", $this->payment_method);
            $stmt->bindParam(":name", $this->name);
            $stmt->bindParam(":phone", $this->phone);
            $stmt->bindParam(":area", $this->area);
            $stmt->bindParam(":zone", $this->zone);
            $stmt->bindParam(":expire_date", $this->expire_date);
            $stmt->bindParam(":ppp_name", $this->ppp_name);
            $stmt->bindParam(":ppp_pass", $this->ppp_pass);
            $stmt->bindParam(":pkg_id", $this->pkg_id);
            $stmt->bindParam(":expire_date", $current_date);

            if ($stmt->execute()) {
                return true;
            }
            return false;

        } else {
            $query = "UPDATE clients SET
            registered = '1', mode = :mode, payment_method = :payment_method, name = :name, phone = :phone, area = :area,
            zone = :zone, expire_date = :expire_date, ppp_name = :ppp_name, ppp_pass = :ppp_pass, pkg_id = :pkg_id WHERE id = $this->id";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(":mode", $this->mode);
            $stmt->bindParam(":payment_method", $this->payment_method);
            $stmt->bindParam(":name", $this->name);
            $stmt->bindParam(":phone", $this->phone);
            $stmt->bindParam(":area", $this->area);
            $stmt->bindParam(":zone", $this->zone);
            $stmt->bindParam(":expire_date", $this->expire_date);
            $stmt->bindParam(":ppp_name", $this->ppp_name);
            $stmt->bindParam(":ppp_pass", $this->ppp_pass);
            $stmt->bindParam(":pkg_id", $this->pkg_id);

            if ($stmt->execute()) {
                return true;
            }
            return false;
        }
    }


    //client search
    function clientSearch()
    {
        //query
        $query = "SELECT * FROM clients
                    WHERE (id LIKE '%$this->search_key%' OR name LIKE '%$this->search_key%' OR phone LIKE '%$this->search_key%'
            OR ppp_name LIKE '%$this->search_key%' OR area LIKE '%$this->search_key%' )";

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        //query execute
        $stmt->execute();
        return $stmt;
    }
}
