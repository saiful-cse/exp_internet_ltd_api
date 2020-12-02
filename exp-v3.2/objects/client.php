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
    public $id, $over_day, $mode, $name, $phone, $address, $email, $area, 
    $int_conn_type, $username, $pasword, $onu_mac, $speed, $fee, $bill_type,
    $reg_date, $active_date, $inactive_date, 
    $last_id, $current_date;
    /*
     * Constructor with $db as database connection
     */
    public function __construct($db)
    {
        $this->conn = $db;
    }


    // ------------------ Active --------------------
    /*
     * Function for active client list
     */
    function active_client()
    {
        //query
        $query = "SELECT id, name, phone, area 
                  FROM client 
                  WHERE mode = 'active' 
                  ORDER BY id 
                  DESC LIMIT 15";

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
        $query = "SELECT id, name, phone, area  
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
        $query = "SELECT id, name, phone , area 
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
        $query = "SELECT id, name, phone , area 
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
    
    /*
     * Function for inactive client list
     */
    function alert_client()
    {
        
        /*
         * Extract alert client
         */
        $query2 = "SELECT id, name, phone, area   FROM client 
                   WHERE DATE_ADD(active_date, INTERVAL 30 DAY) <= '$this->current_date' AND mode = 'Active'
                   ORDER BY id 
                   DESC LIMIT 15";
        // prepare query statement
        $stmt2 = $this->conn->prepare($query2);
        //query execute
        $stmt2->execute();
        return $stmt2;
    }


    /*
     * Function for more inactive client list
     */
    function more_alert_client()
    {
        
        /*
         * Extract alert client
         */
        $query2 = "SELECT id, name, phone, area   FROM client 
                   WHERE DATE_ADD(active_date, INTERVAL 30 DAY) <= '$this->current_date' AND id < '$this->last_id' AND mode = 'Active'
                   ORDER BY id 
                   DESC LIMIT 15";
        // prepare query statement
        $stmt2 = $this->conn->prepare($query2);
        //query execute
        $stmt2->execute();
        return $stmt2;
    }
    
    //Over 3day client list
    function over3Day_client()
    {
        
        /*
         * Extract alert client
         */
        $query2 = "SELECT id, name, phone, area   FROM client 
                   WHERE DATE_ADD(active_date, INTERVAL 33 DAY) <= '$this->current_date' AND mode = 'Active'
                   ORDER BY id 
                   DESC LIMIT 15";
        // prepare query statement
        $stmt2 = $this->conn->prepare($query2);
        //query execute
        $stmt2->execute();
        return $stmt2;
    }

    //Over more3day client list
    function more_over3Day_client()
    {
        
        /*
         * Extract alert client
         */
        $query2 = "SELECT id, name, phone, area   FROM client 
                   WHERE DATE_ADD(active_date, INTERVAL 33 DAY) <= '$this->current_date' AND id < '$this->last_id' AND mode = 'Active'
                   ORDER BY id 
                   DESC LIMIT 15";
        // prepare query statement
        $stmt2 = $this->conn->prepare($query2);
        //query execute
        $stmt2->execute();
        return $stmt2;
    }


    function count_alert_client()
    {
        
        //query
        $query = "SELECT COUNT(*) FROM client WHERE DATE_ADD(active_date, INTERVAL 30 DAY) <= '$this->current_date' AND mode = 'Active'";

        //prepare query
        $stmt = $this->conn->prepare($query);

        //execute query
        if ($stmt->execute())
        {
            return $stmt->fetchColumn();
        }

        return false;
    }

    

    //------ Get Username -------

    function username()
    {
        
        $query = "SELECT id, name, username, mode FROM client
                    
                   ORDER BY LPAD(LOWER(username), 10,0) DESC";
        // prepare query statement
        $stmt2 = $this->conn->prepare($query);
        //query execute
        $stmt2->execute();
        return $stmt2;
    }

    /*
     * Function for client details
     */
    function client_details()
    {

        $current_date = date("Y-m-d H:i:s");
        //query
        $query = "SELECT * FROM client WHERE id = '$this->id'";

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        //query execute
        $stmt->execute();
        return $stmt;
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

        if ($this->mode == $current_mode)
        {
            $query = "UPDATE client
                      SET 
                      name = :name, phone = :phone, address = :address, email = :email, area = :area,
                      int_conn_type = :int_conn_type, username = :username, password = :password, onu_mac = :onu_mac, speed = :speed, fee = :fee, bill_type = :bill_type
                      WHERE id = '$this->id'";

            //prepare query
            $stmt = $this->conn->prepare($query);

            //Bind Value
            $stmt->bindParam(":name",$this->name);
            $stmt->bindParam(":phone",$this->phone);
            $stmt->bindParam(":address",$this->address);
            $stmt->bindParam(":email",$this->email);
            $stmt->bindParam(":area",$this->area);

            $stmt->bindParam(":int_conn_type",$this->int_conn_type);
            $stmt->bindParam(":username",$this->username);
            $stmt->bindParam(":password",$this->password);
            $stmt->bindParam(":onu_mac",$this->onu_mac);

            $stmt->bindParam(":speed",$this->speed);
            $stmt->bindParam(":fee",$this->fee);
            $stmt->bindParam(":bill_type",$this->bill_type);

            //Execute query
            if ($stmt->execute())
            {
                return true;
            }
            return false;

        }else if($this->mode != "Inactive")
        {
            $query = "UPDATE client
                      SET 
                      mode = :mode, active_date = :active_date, name = :name, phone = :phone, address = :address, email = :email, area = :area,
                      int_conn_type = :int_conn_type, username = :username, password = :password, onu_mac = :onu_mac,
                      speed = :speed, fee = :fee, bill_type = :bill_type
                      WHERE id = '$this->id'";

            //prepare query
            $stmt = $this->conn->prepare($query);

            //Bind Value
            $stmt->bindParam(":mode",$this->mode);
            $stmt->bindParam(":active_date",$this->active_date);
            $stmt->bindParam(":name",$this->name);
            $stmt->bindParam(":phone",$this->phone);
            $stmt->bindParam(":address",$this->address);
            $stmt->bindParam(":email",$this->email);
            $stmt->bindParam(":area",$this->area);

            $stmt->bindParam(":int_conn_type",$this->int_conn_type);
            $stmt->bindParam(":username",$this->username);
            $stmt->bindParam(":password",$this->password);
            $stmt->bindParam(":onu_mac",$this->onu_mac);

            $stmt->bindParam(":speed",$this->speed);
            $stmt->bindParam(":fee",$this->fee);
            $stmt->bindParam(":bill_type",$this->bill_type);

            //Execute query
            if ($stmt->execute())
            {
                return true;
            }
            return false;

        }else if($this->mode != "Active")
        {
            $query = "UPDATE client SET 
                      mode = :mode, inactive_date = :inactive_date, name = :name, phone = :phone, address = :address, email = :email, area = :area,
                      int_conn_type = :int_conn_type, username = :username, password = :password, onu_mac = :onu_mac,
                      speed = :speed, fee = :fee, bill_type = :bill_type WHERE id = '$this->id'";

            //prepare query
            $stmt = $this->conn->prepare($query);

            //Bind Value
            $stmt->bindParam(":mode",$this->mode);
            $stmt->bindParam(":inactive_date",$this->inactive_date);
            $stmt->bindParam(":name",$this->name);
            $stmt->bindParam(":phone",$this->phone);
            $stmt->bindParam(":address",$this->address);
            $stmt->bindParam(":email",$this->email);
            $stmt->bindParam(":area",$this->area);

            $stmt->bindParam(":int_conn_type",$this->int_conn_type);
            $stmt->bindParam(":username",$this->username);
            $stmt->bindParam(":password",$this->password);
            $stmt->bindParam(":onu_mac",$this->onu_mac);

            $stmt->bindParam(":speed",$this->speed);
            $stmt->bindParam(":fee",$this->fee);
            $stmt->bindParam(":bill_type",$this->bill_type);

            //Execute query
            if ($stmt->execute())
            {
                return true;
            }
            return false;

        }
    }

    function profile(){

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
        $stmt->bindParam(":name",$this->name);
        $stmt->bindParam(":address",$this->address);
        $stmt->bindParam(":email",$this->email);

        //Execute query
        if ($stmt->execute())
        {
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
        $stmt->bindParam(":name",$this->name);
        $stmt->bindParam(":phone",$this->phone);

        //Execute query
        if ($stmt->execute())
        {
            return true;
        }
        return false;
    }

    // check user is exist
    function check(){

        // query to select record
        $query = "SELECT phone FROM client WHERE phone = :phone";

        // prepare query
        $stmt = $this->conn->prepare($query);

        //bind value
        $stmt->bindParam(":phone",$this->phone);

        // execute query
        $stmt->execute();
        return $stmt;
    }


}