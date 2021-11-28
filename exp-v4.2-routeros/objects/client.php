<?php

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
    public $id, $registered, $mode, $payment_method, $name, $phone, $area,
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
        $query = "SELECT * FROM clients WHERE registered = 1 ORDER BY id DESC";

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        //query execute
        $stmt->execute();
        return $stmt;
    }

    function unregistered_client()
    {
        //query
        $query = "SELECT * FROM clients WHERE registered = 0 ORDER BY id DESC";

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        //query execute
        $stmt->execute();
        return $stmt;
    }

    /*
     * Function for more active client list
     */
    function more_active_client()
    {
        //query
        $query = "SELECT id, name, phone, area , username, payment_method
                  FROM client 
                  WHERE mode = 'active' AND id < '$this->last_id'
                  ORDER BY id
                  DESC LIMIT 15";

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        //query execute
        $stmt->execute();
        return $stmt;
    }


    // ------------------ Inactive --------------------
    /*
     * Function for inactive client list
     */
    function inactive_client()
    {
        //query
        $query = "SELECT id, name, phone, area, username
                  FROM client 
                  WHERE mode = 'inactive' 
                  ORDER BY id 
                  DESC LIMIT 15";

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        //query execute
        $stmt->execute();
        return $stmt;
    }

    /*
     * Function for more inactive client list
     */
    function more_inactive_client()
    {
        //query
        $query = "SELECT id, name, phone , area, username
                  FROM client 
                  WHERE mode = 'inactive' AND id < '$this->last_id'
                  ORDER BY id
                  DESC LIMIT 15";

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        //query execute
        $stmt->execute();
        return $stmt;
    }


    // ------------------ Alert --------------------

    function all_alert_client()
    {

        /*
         * Extract alert client
         */
        $query2 = "SELECT id, name, phone, area , username
                    FROM client 
                   WHERE DATE_ADD(active_date, INTERVAL 1 MONTH) <= '$this->current_date' AND mode = 'Active'
                   ORDER BY id DESC";
        // prepare query statement
        $stmt2 = $this->conn->prepare($query2);
        //query execute
        $stmt2->execute();
        return $stmt2;
    }

    function count_alert_client()
    {

        //query
        $query = "SELECT COUNT(*) FROM client WHERE DATE_ADD(active_date, INTERVAL 1 MONTH) <= '$this->current_date' AND mode = 'Active'";

        //prepare query
        $stmt = $this->conn->prepare($query);

        //execute query
        if ($stmt->execute()) {
            return $stmt->fetchColumn();
        }

        return false;
    }


    function enabled_client()
    {

        //query
        $query = "SELECT * FROM clients WHERE registered = '1' AND mode = 'enable'";

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        //query execute
        $stmt->execute();
        return $stmt;
    }

    function diabled_client()
    {

        //query
        $query = "SELECT * FROM clients WHERE registered = '1' AND mode = 'disable'";

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
        $query = "SELECT * FROM clients WHERE expire_date <= '$this->current_date' AND registered = '1' AND mode = 'enable'";

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
        $query = "INSERT INTO clients 
        SET registered = '0', name = :name, phone = :phone, area = :area";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":phone", $this->phone);
        $stmt->bindParam(":area", $this->area);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    function client_details_update()
    {
        $query = "UPDATE clients SET
        registered = '1', payment_method = :payment_method, name = :name, phone = :phone, area = :area,
        ppp_name = :ppp_name, ppp_pass = :ppp_pass, pkg_id = :pkg_id WHERE id = $this->id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":payment_method", $this->payment_method);
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":phone", $this->phone);
        $stmt->bindParam(":area", $this->area);
        $stmt->bindParam(":ppp_name", $this->ppp_name);
        $stmt->bindParam(":ppp_pass", $this->ppp_pass);
        $stmt->bindParam(":pkg_id", $this->pkg_id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    /*
     * Function for client data update
     */
    function data_update()
    {

        $query1 = "SELECT mode FROM client WHERE id = '$this->id'";

        $stmt = $this->conn->query($query1);
        $f = $stmt->fetch();
        $current_mode = $f['mode'];

        if ($this->mode == $current_mode) {
            $query = "UPDATE client
                      SET 
                      name = :name, phone = :phone, address = :address, email = :email, area = :area,
                      username = :username, password = :password,
                      speed = :speed, fee = :fee, payment_method = :payment_method
                      WHERE id = '$this->id'";

            //prepare query
            $stmt = $this->conn->prepare($query);

            //Bind Value
            $stmt->bindParam(":name", $this->name);
            $stmt->bindParam(":phone", $this->phone);
            $stmt->bindParam(":address", $this->address);
            $stmt->bindParam(":email", $this->email);
            $stmt->bindParam(":area", $this->area);

            $stmt->bindParam(":username", $this->username);
            $stmt->bindParam(":password", $this->password);

            $stmt->bindParam(":speed", $this->speed);
            $stmt->bindParam(":fee", $this->fee);
            $stmt->bindParam(":payment_method", $this->payment_method);

            //Execute query
            if ($stmt->execute()) {
                return true;
            }
            return false;
        } else if ($this->mode != "Inactive") {
            $query = "UPDATE client
                      SET 
                      mode = :mode, active_date = :active_date, name = :name, phone = :phone, address = :address, email = :email, area = :area,
                      username = :username, password = :password,
                      speed = :speed, fee = :fee, payment_method = :payment_method
                      WHERE id = '$this->id'";

            //prepare query
            $stmt = $this->conn->prepare($query);

            //Bind Value
            $stmt->bindParam(":mode", $this->mode);
            $stmt->bindParam(":active_date", $this->active_date);
            $stmt->bindParam(":name", $this->name);
            $stmt->bindParam(":phone", $this->phone);
            $stmt->bindParam(":address", $this->address);
            $stmt->bindParam(":email", $this->email);
            $stmt->bindParam(":area", $this->area);

            $stmt->bindParam(":username", $this->username);
            $stmt->bindParam(":password", $this->password);

            $stmt->bindParam(":speed", $this->speed);
            $stmt->bindParam(":fee", $this->fee);
            $stmt->bindParam(":payment_method", $this->payment_method);

            //Execute query
            if ($stmt->execute()) {
                return true;
            }
            return false;
        } else if ($this->mode != "Active") {
            $query = "UPDATE client SET 
                      mode = :mode, inactive_date = :inactive_date, name = :name, phone = :phone, address = :address, email = :email, area = :area,
                      username = :username, password = :password,
                      speed = :speed, fee = :fee, payment_method = :payment_method
                      WHERE id = '$this->id'";

            //prepare query
            $stmt = $this->conn->prepare($query);

            //Bind Value
            $stmt->bindParam(":mode", $this->mode);
            $stmt->bindParam(":inactive_date", $this->inactive_date);
            $stmt->bindParam(":name", $this->name);
            $stmt->bindParam(":phone", $this->phone);
            $stmt->bindParam(":address", $this->address);
            $stmt->bindParam(":email", $this->email);
            $stmt->bindParam(":area", $this->area);

            $stmt->bindParam(":username", $this->username);
            $stmt->bindParam(":password", $this->password);

            $stmt->bindParam(":speed", $this->speed);
            $stmt->bindParam(":fee", $this->fee);
            $stmt->bindParam(":payment_method", $this->payment_method);

            //Execute query
            if ($stmt->execute()) {
                return true;
            }
            return false;
        }
    }

    function profile()
    {

        $query = "SELECT * FROM client WHERE phone = :phone";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":phone", $this->phone);

        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // set values to object properties
        $this->id = $row['id'];
        $this->mode = $row['mode'];
        $this->alert = $row['alert'];
        $this->name = $row['name'];
        $this->phone = $row['phone'];
        $this->email = $row['email'];
        $this->address = $row['address'];

        $this->int_conn_type = $row['int_conn_type'];
        $this->username = $row['username'];
        $this->password = $row['password'];
        $this->onu_mac = $row['onu_mac'];
        $this->speed = $row['speed'];
        $this->fee = $row['fee'];
        $this->bill_type = $row['bill_type'];

        $this->reg_date = $row['reg_date'];
        $this->active_date = $row['active_date'];
        $this->inactive_date = $row['inactive_date'];
    }

    function profile_update()
    {
        $query = "UPDATE client
                  SET 
                  name = :name, address = :address, email = :email
                  WHERE id = '$this->id'";
        //prepare query
        $stmt = $this->conn->prepare($query);

        //Bind Value
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":address", $this->address);
        $stmt->bindParam(":email", $this->email);

        //Execute query
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    //user registration
    function registration()
    {

        $query = "INSERT INTO client
                  SET 
                  mode = 'Inactive', name = :name, phone = :phone";


        //prepare query
        $stmt = $this->conn->prepare($query);

        //Bind Value
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":phone", $this->phone);

        //Execute query
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // check user is exist
    function check()
    {

        // query to select record
        $query = "SELECT phone FROM client WHERE phone = :phone";

        // prepare query
        $stmt = $this->conn->prepare($query);

        //bind value
        $stmt->bindParam(":phone", $this->phone);

        // execute query
        $stmt->execute();
        return $stmt;
    }

    //client search
    function clientSearch()
    {
        //query
        $query = "SELECT id, name, phone, username, area , mode FROM client
                    WHERE (id LIKE '%$this->search_key%' OR name LIKE '%$this->search_key%' OR phone LIKE '%$this->search_key%'
            OR username LIKE '%$this->search_key%' OR area LIKE '%$this->search_key%' )";

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        //query execute
        $stmt->execute();
        return $stmt;
    }

    //All active client fetch
    function allClient()
    {
        $q = "SELECT * FROM client WHERE mode = 'Active'";
        $stmt = $this->conn->prepare($q);
        $stmt->execute();
        return $stmt;
    }
}
