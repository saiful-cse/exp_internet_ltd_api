<?php
// used to get mysql database connection
class Database{
 
    // specify your own database credentials
    // private $host = "localhost";
    // private $db_name = "creative_exp-v43-test";
    // private $username = "creative_exp-v43-test";
    // private $password = "exp_test12345";
    // private $conn;
    private $host = "localhost";
    private $db_name = "creative_exp-v43";
    private $username = "creative_exp-v43";
    private $password = "creative_exp-v43";
    public $conn;
 
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