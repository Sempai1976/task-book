<?php
namespace TaskBook;

use \TaskBook\DataSource;

class Task
{
    private $ds;

    function __construct()
    {
        require_once "DataSource.php";
        $this->ds = new DataSource();
    }

    public function getConnection() {
		return $this->ds->getConnection();
	}

    public function getTaskById($id)
    {
        $query = "select * FROM tasks WHERE id = ?";
        $paramType = "i";
        $paramArray = array($id);
        $taskResult = $this->ds->select($query, $paramType, $paramArray);
        
        return $taskResult;
    }
    
    public function getTaskIdByHash($hash)
    {
        $query = "select id FROM tasks WHERE user_hash = ?";
        $paramType = "s";
        $paramArray = array($hash);
        $taskResult = $this->ds->select($query, $paramType, $paramArray);
        
        return $taskResult;
    }

    public function getAllTaskCount($where)
    {
        $query = "SELECT * FROM tasks AS t ".$where;
        $taskCount = $this->ds->numRows($query);
        return $taskCount;
    }

    public function addTask($paramArray = []) {
        $query = "INSERT INTO `tasks` (`poster_id`, `user_name`, `email`, `task`, `created`, `user_hash`)
VALUES (?, ?, ?, ?, ?, ?)";
        $paramType = "isssis";
        $insertResult = $this->ds->insert($query, $paramType, $paramArray);
        if($insertResult) {
            return $insertResult;
        }
        
        return false;
    }
    
    public function updateTask($paramArray = []) {
        $query = "UPDATE tasks SET user_name = ?, email = ?, task = ?, updated = ?, status = ? WHERE id = ?";
        $paramType = "sssiii";
        $updateResult = $this->ds->update($query, $paramType, $paramArray);
        return $updateResult;
    }

    public function deleteTask($id)
    {
        $query = "DELETE FROM tasks WHERE id = ?";
        $paramType = "i";
        $paramArray = array($id);
        $this->ds->delete($query, $paramType, $paramArray);
    }
}