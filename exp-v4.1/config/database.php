<?php


class Database{
    //Specify the own database credentials
    private $host = "localhost";
    private $db_name = "creative_expert_internet";
    private $username = "creative_exp_int";
    private $password = "saifulcse0001";
    private $conn;

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