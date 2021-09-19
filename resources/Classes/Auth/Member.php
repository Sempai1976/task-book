<?php
namespace Аuth;

use \Аuth\DataSource;

class Member
{
    private $ds;
    private $user_id;
    private $group_id;
    private $user_name;
    private $full_name;
    private $email;
    private $is_user_logged = false;
    private $is_admin = false;

    function __construct()
    {
        require_once "DataSource.php";
        $this->ds = new DataSource();
        $this->_init();
    }

    protected function _init() {
		if (isset($_SESSION["is_user_logged"])) {
			$this->set_userdata();
		}
	}
	
	public function set_userdata() {
		$this->user_id = $_SESSION["user_id"];
        $this->group_id = $_SESSION["group_id"];
        $this->user_name = $_SESSION["user_name"];
        $this->full_name = $_SESSION["full_name"];
        $this->email = $_SESSION["email"];
        $this->is_user_logged = $_SESSION["is_user_logged"];
        $this->is_admin = ($_SESSION["group_id"] === ADMIN_GROUP) ? true : false;
	}
	
	public function unset_userdata() {
		$this->user_id = "";
        $this->group_id = "";
        $this->user_name = "";
        $this->full_name = "";
        $this->email = "";
        $this->is_user_logged = false;
        $this->is_admin = false;
	}
	
	public function get_userdata() {
		$data = [
		    'user_id' => $this->user_id,
            'group_id' => $this->group_id,
            'user_name' => $this->user_name,
            'full_name' => $this->full_name,
            'email' => $this->email,
            'is_user_logged' => $this->is_user_logged,
            'is_admin' => $this->is_admin
		];

        return $data;
	}

	public function get_user_id() {
		return $this->user_id;
	}
	
	public function get_group_id() {
		return $this->group_id;
	}
	
	public function get_user_name() {
		return $this->user_name;
	}
	
	public function get_full_name() {
		return $this->full_name;
	}
	
	public function get_name() {
		return !empty($this->full_name) ? $this->full_name : $this->user_name;
	}
	
	public function get_email() {
		return $this->email;
	}
	
	public function is_user_logged() {
		return $this->is_user_logged;
	}
	
	public function is_admin() {
		return $this->is_admin;
	}
    
    public function getConnection() {
		return $this->ds->getConnection();
	}

    public function getMemberById($id)
    {
        $query = "select * FROM users WHERE id = ?";
        $paramType = "i";
        $paramArray = array($id);
        $memberResult = $this->ds->select($query, $paramType, $paramArray);
        
        return $memberResult;
    }
    
    public function login($username, $password) {
        $query = "select * FROM users WHERE user_name = ?";
        $paramType = "s";
        $paramArray = array($username);
        $result = $this->ds->select($query, $paramType, $paramArray);
        if(!empty($result) && password_verify($password, $result[0]['password'])) {
//          $_SESSION["userId"] = $memberResult[0]["id"];
            return $result;
        }
    }
    
    public function createMember($paramArray = []) {
        $query = "INSERT INTO `users` (`group_id`, `user_name`, `full_name`, `email`, `password`)
VALUES (?, ?, ?, ?, ?)";
        $paramType = "issss";
        $insertResult = $this->ds->insert($query, $paramType, $paramArray);
        if($insertResult) {
            return $insertResult;
        }
        
        return false;
    }
    
    public function updateMember($paramArray = []) {
        $query = "UPDATE users SET group_id = ?, user_name = ?, full_name = ?, email = ?, password = ? WHERE id = ?";
        $paramType = "issssi";
        $updateResult = $this->ds->update($query, $paramType, $paramArray);
        return $updateResult;
    }

    public function deleteMember($id)
    {
        $query = "DELETE FROM users WHERE id = ?";
        $paramType = "i";
        $paramArray = array($id);
        $this->ds->delete($query, $paramType, $paramArray);
    }
    
    public function isDupeField($field, $type, $value)
    {
        $query = "select * FROM users WHERE ".$field." = ?";
        $result = $this->ds->numRows($query, $type, [$value]);
        if ($result > 0) {
			return true;
		}
        return false;
    }
}