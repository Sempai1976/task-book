<?php
namespace Autologin;

use \Autologin\DataSource;
use \Аuth\Member;

class Sessions
{
    private $ds;
    private $cookie_name;
    private $cookie_life;
    private $user_agent;
    private $user_ip;

    function __construct()
    {
        if (!isset($_SESSION)) {
            session_start();
        }
        require_once "DataSource.php";
        $this->ds = new DataSource();
        $this->cookie_name = AUTOLOGIN_COOKIE_NAME;
        $this->cookie_life = AUTOLOGIN_COOKIE_LIFE;
        $this->user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? substr($_SERVER['HTTP_USER_AGENT'], 0, 149) : '';
        $this->user_ip = get_user_ip();
    }

    public function check_session() {
		if (!isset($_SESSION["is_user_logged"])) {
			$this->check_cookies();
			$this->delete_old_autologin_data();
		}
	}
	
	private function check_cookies() {
		if (isset($_COOKIE[$this->cookie_name.'_key'])) {
            $user_id = $this->get_autologin_data($_COOKIE[$this->cookie_name.'_key']);
            if ($user_id) {
				$member = new Member();
				$user_data = $member->getMemberById($user_id);
				if ($user_data) {
					$this->set_session($user_data);
					$member->set_userdata();
				}
			}
		}
	}
	
	public function set_session($data = []) {
		$_SESSION["user_id"] = $data[0]["id"];
    	$_SESSION["group_id"] = $data[0]["group_id"];
    	$_SESSION["user_name"] = $data[0]["user_name"];
    	$_SESSION["full_name"] = $data[0]["full_name"];
    	$_SESSION["email"] = $data[0]["email"];
    	$_SESSION["is_admin"] = ($data[0]["group_id"] === ADMIN_GROUP) ? true : false;
    	$_SESSION["is_user_logged"] = true;
	}
	
	private function get_autologin_data($key) {
		$modif_key = md5($key);
        $query = "select * FROM autologin WHERE key_id = ?";
        $paramType = "s";
        $paramArray = array($modif_key);
        $result = $this->ds->select($query, $paramType, $paramArray);
        if(!empty($result)) {
            return $result[0]["user_id"];
        }
        return false;
    }
    
    public function set_autologin_cookie() {
		if (isset($_SESSION["user_id"])) {
			$user_id = $_SESSION["user_id"];
			$key_id = substr(md5(uniqid(time())), 0, 32); //md5(time());
			$user_agent = $this->user_agent;
			$user_ip = $this->user_ip;
			$last_login = time();
			setcookie($this->cookie_name.'_key', $key_id, time() + $this->cookie_life, '/');
			setcookie($this->cookie_name.'_agent', $user_agent, time() + $this->cookie_life, '/');
			setcookie($this->cookie_name.'_ip', $user_ip, time() + $this->cookie_life, '/');
			$this->delete_autologin_data([$user_id, $user_agent, $user_ip], false);
			$this->set_autologin_data([$user_id, md5($key_id), $user_agent, $user_ip, $last_login]);
		}
	}
    
    private function set_autologin_data($paramArray = []) {
        $query = "INSERT INTO `autologin` (`user_id`, `key_id`, `user_agent`, `user_ip`, `last_login`)
VALUES (?, ?, ?, ?, ?)";
        $paramType = "isssi";
        $this->ds->insert($query, $paramType, $paramArray);
    }

    public function delete_autologin_cookie() {
		if (isset($_SESSION["user_id"])) {
			$user_id = $_SESSION["user_id"];
			$user_agent = $this->user_agent; 
			$user_ip = $this->user_ip;
			setcookie($this->cookie_name.'_key', '', -1, '/');
			setcookie($this->cookie_name.'_agent', '', -1, '/');
			setcookie($this->cookie_name.'_ip', '', -1, '/');
			$this->delete_autologin_data([$user_id, $user_agent, $user_ip], false);
		}
	}

    private function delete_autologin_data($paramArray = [], $delete_all = false)
    {
        if ($delete_all) {
			$query = "DELETE FROM autologin WHERE user_id = ?";
            $paramType = "i";
		} else {
			$query = "DELETE FROM autologin WHERE user_id = ? AND user_agent = ? AND user_ip = ?";
            $paramType = "iss";
		}
        $this->ds->delete($query, $paramType, $paramArray);
    }
    
    private function delete_old_autologin_data()
    {
		$date = time() - $this->cookie_life;
		$query = "DELETE FROM autologin WHERE last_login < ?";
        $paramType = "i";
        $paramArray = array($date);
        $this->ds->delete($query, $paramType, $paramArray);
    }
}