<?php

class Database{
    //Specify the own database credentials
    // private $host = "localhost";
    // private $db_name = "exp";
    // private $username = "root";
    // private $password = "";
    // private $conn;
    
    private $host = "localhost";
    private $db_name = "creative_exp-v43";
    private $username = "creative_exp-v43";
    private $password = "creative_exp-v43";
    public $conn;

    //Get the database connection
    public function getConnection(){
        $this->conn = null;

        try{
            $this->conn = new PDO("mysql:host=".$this->host.";dbname=".$this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
        }catch (PDOException $exception){
            echo "Connection error: ".$exception->getMessage();
        }
        return $this->conn;
    }
}