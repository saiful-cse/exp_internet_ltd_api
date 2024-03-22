<?php
include_once '../config/database.php';

class Employee
{
    /*
     * Database connection
     */
    private $conn;

    /*
     * Objects properties
     */
    public $id, $employee_id, $name, $address, $mobile, $about, $pin, $super_admin, $dashboard,
        $client_add, $client_details_update, $sms, $txn_summary, $txn_edit, $upstream_bill,
        $salary_add, $device, $note, $created_at, $details;
    /*
     * Constructor with $db as database connection
     */
    public function __construct($db)
    {
        $this->conn = $db;
    }

    //admin login
    function login()
    {
        //query
        $query = "SELECT * FROM employees 
        WHERE employee_id = :employee_id AND pin = :pin";

        // prepare query statement
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":employee_id", $this->employee_id);
        $stmt->bindParam(":pin", $this->pin);

        $stmt->execute();

        return $stmt;
    }

    function employee_list()
    {
        //query
        $query = "SELECT id, name, employee_id FROM employees";

        // prepare query statement
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    function employee_details()
    {
        //query
        $query = "SELECT * FROM employees WHERE id = '$this->id' ";

        // prepare query statement
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    function login_record()
    {

        $current_date =  date("Y-m-d H:i:s");
        $query = "INSERT INTO logs 
        SET time = :current_date, details = :details";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":details", $this->details);
        $stmt->bindParam(":current_date", $current_date);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    function fetch_logs()
    {
        //query
        $query = "SELECT * FROM logs ORDER BY time DESC LIMIT 10";

        // prepare query statement
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    function employee_id_is_exist()
    {
        $query = "SELECT employee_id FROM employees WHERE employee_id = '$this->employee_id' && id != '$this->id'";
        // prepare the query
        $stmt = $this->conn->prepare($query);

        // execute the query
        $stmt->execute();
        // get number of rows
        $num = $stmt->rowCount();

        if ($num > 0) {

            return true;
        }
        return false;
    }


    function employee_details_update()
    {
        $query = "UPDATE employees SET employee_id = :employee_id, name = :name, address = :address, mobile = :mobile, about = :about, 
        pin = :pin, super_admin = :super_admin,
        dashboard = :dashboard, client_add = :client_add, client_details_update = :client_details_update, 
        sms = :sms, txn_summary = :txn_summary, txn_edit = :txn_edit, upstream_bill = :upstream_bill, 
        salary_add = :salary_add, device = :device, note = :note
        WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":employee_id", $this->employee_id);
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":address", $this->address);
        $stmt->bindParam(":mobile", $this->mobile);
        $stmt->bindParam(":about", $this->about);
        $stmt->bindParam(":pin", $this->pin);
        $stmt->bindParam(":super_admin", $this->super_admin);
        $stmt->bindParam(":dashboard", $this->dashboard);
        $stmt->bindParam(":client_add", $this->client_add);
        $stmt->bindParam(":client_details_update", $this->client_details_update);
        $stmt->bindParam(":sms", $this->sms);
        $stmt->bindParam(":txn_summary", $this->txn_summary);
        $stmt->bindParam(":txn_edit", $this->txn_edit);
        $stmt->bindParam(":upstream_bill", $this->upstream_bill);
        $stmt->bindParam(":salary_add", $this->salary_add);
        $stmt->bindParam(":device", $this->device);
        $stmt->bindParam(":note", $this->note);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    function employee_add()
    {
        $query = "INSERT INTO employees SET employee_id = :employee_id, name = :name, address = :address, mobile = :mobile, about = :about, pin = :pin";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":employee_id", $this->employee_id);
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":address", $this->address);
        $stmt->bindParam(":mobile", $this->mobile);
        $stmt->bindParam(":about", $this->about);
        $stmt->bindParam(":pin", $this->pin);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    function employee_delete()
    {
        $query = "DELETE FROM employees WHERE id = '$this->id'";
        $stmt = $this->conn->prepare($query);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
