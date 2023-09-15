<?php

include_once './config/database.php';

class Client
{
    /*
     * Database connection
     */
    private $conn;

    /*
     * Objects properties
     */
    public $id, $phone;

    /*
     * Constructor with $db as database connection
     */
    public function __construct($db)
    {
        $this->conn = $db;
    }

    function client_details()
    {

        //query
        $query = "SELECT clients.*, packages.pkg_id, packages.title, packages.speed, packages.price
        FROM clients
        INNER JOIN packages ON clients.pkg_id = packages.pkg_id
        WHERE clients.phone = '$this->phone'";

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        //query execute
        $stmt->execute();
        return $stmt->fetch();
    }


    function txn_store()
    {
        try {

            $this->conn->beginTransaction();
            $query = "INSERT INTO txn_list 
              SET client_id = :client_id, name = :name, date = :date, credit = :credit, 
              type = :type, details = :details, method = :method, emp_id = :emp_id";

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
            $stmt->bindParam(":emp_id", $this->emp_id);

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
