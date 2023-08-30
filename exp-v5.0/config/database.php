<?php
// used to get mysql database connection
class Database{
 
    // specify your own database credentials
    // private $host = "localhost";
    // private $db_name = "u650347749_expert_int";
    // private $username = "u650347749_saif_ullah";
    // private $password = "Saiful@#21490";
    // public $conn;

    private $host = "localhost";
    private $db_name = "exp";
    private $username = "root";
    private $password = "";
    private $conn;
 
    // get the database connection
    public function getConnection(){
 
        $this->conn = null;
 
        try{
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
        }catch(PDOException $exception){
            echo "Connection error: " . $exception->getMessage();
        }
 
        return $this->conn;
    }
}
?>