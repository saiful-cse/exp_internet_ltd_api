<?php
date_default_timezone_set("Asia/Dhaka");
include_once '../config/database.php';

class Note
{
    private $conn;

    public $id, $note, $updated_at;
    /*
     * Constructor with $db as database connection
     */
    public function __construct($db)
    {
        $this->conn = $db;
    }

    function getNote()
    {
        $query = "SELECT * FROM notes WHERE id = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch();
    }

    function updateNote()
    {
        $query = "UPDATE notes SET note = :note, updated_at = :updated_at WHERE id = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":note", $this->note);
        $stmt->bindParam(":updated_at", $this->updated_at);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

}

?>