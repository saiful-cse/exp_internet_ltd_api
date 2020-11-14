<?php

include_once '../config/database.php';

class News
{
    /*
     * Database connection
     */
    private $conn;

    /*
     * Objects properties
     */
    public $id;
    public $title;
    public $description;
    public $image_path;
    public $created_at;

    /*
     * Constructor with $db as database connection
     */
    public function __construct($db)
    {
        $this->conn = $db;
    }

    /*
     * Create post
     */
    function create()
    {
        //query
        $query = "INSERT INTO news SET title = :title, description = :description, image_path = :image_path";

        //prepare query
        $stmt = $this->conn->prepare($query);

        //sanitize
        $this->title = htmlspecialchars($this->title);
        $this->description = htmlspecialchars($this->description);
        $this->image_path = htmlspecialchars($this->image_path);

        //Bind Value
        $stmt->bindParam(":title",$this->title);
        $stmt->bindParam(":description",$this->description);
        $stmt->bindParam(":image_path",$this->image_path);

        //Execute query
        if ($stmt->execute())
        {
            return true;
        }
        return false;

    }

    /*
     * Function for news load
     */
    function news_load()
    {
        $query = "SELECT * FROM news 
                  ORDER BY id 
                  DESC LIMIT 5";

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        //query execute
        $stmt->execute();
        return $stmt;
    }

    /*
     * Function for news load
     */
    function more_news_load()
    {
        $query = "SELECT * FROM news 
                  WHERE id < '$this->id'
                  ORDER BY id 
                  DESC LIMIT 5";

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        //query execute
        $stmt->execute();
        return $stmt;
    }


    function update()
    {
        //query
        $query = "UPDATE news 
                  SET title = :title, description = :description, image_path = :image_path
                  WHERE id = '$this->id'";

        //prepare query
        $stmt = $this->conn->prepare($query);

        //sanitize
        $this->title = htmlspecialchars($this->title);
        $this->description = htmlspecialchars($this->description);
        $this->image_path = htmlspecialchars($this->image_path);

        //Bind Value
        $stmt->bindParam(":title",$this->title);
        $stmt->bindParam(":description",$this->description);
        $stmt->bindParam(":image_path",$this->image_path);

        //Execute query
        if ($stmt->execute())
        {
            return true;
        }
        return false;

    }

    //delete news
    function delete(){

        // select all query
        $query = "DELETE FROM news WHERE id = :id";

        // prepare query statement
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);

        // execute query
        if($stmt->execute()){
            return true;
        }
        return false;
    }
}