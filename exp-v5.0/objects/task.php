<?php
include_once '../config/database.php';

class Task
{
    private $conn;


    public $id, $employee_id, $assign_by, $assign_on, $completed, $description, $created_at, $completed_at;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    function get_pending_task()
    {
        $query = "SELECT super_admin FROM employees WHERE employee_id = '$this->employee_id' ";
        $stmt = $this->conn->query($query);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $status = $stmt->fetch();
        } else {
            $status['super_admin'] = 0;
        }

        if ($status['super_admin'] == 1) {
            $query = "SELECT * FROM tasks WHERE completed = 0 order by id asc";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt;
        } else {
            $query = "SELECT * FROM tasks WHERE assign_on = '$this->employee_id' AND completed = 0 order by id asc";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt;
        }
    }

    function get_completed_task()
    {
        $query = "SELECT super_admin FROM employees WHERE employee_id = '$this->employee_id' order by id asc";
        $stmt = $this->conn->query($query);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $status = $stmt->fetch();
        } else {
            $status['super_admin'] = 0;
        }

        if ($status['super_admin'] == 1) {
            $query = "SELECT * FROM tasks WHERE completed = 1";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt;
        } else {
            $query = "SELECT * FROM tasks WHERE assign_on = '$this->employee_id' AND completed = 1 order by id asc";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt;
        }
    }

    function task_completed()
    {
        $query = "UPDATE tasks SET completed = 1, completed_at = '$this->completed_at' WHERE id = '$this->id' ";
        $stmt = $this->conn->prepare($query);
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    function task_update()
    {
        $query = "UPDATE tasks SET description = :description, assign_by = :assign_by, assign_on = :assign_on WHERE id = '$this->id' ";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":assign_by", $this->assign_by);
        $stmt->bindParam(":assign_on", $this->assign_on);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    function task_delete()
    {
        $query = "DELETE FROM tasks WHERE id = '$this->id'";
        $stmt = $this->conn->prepare($query);
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
}
