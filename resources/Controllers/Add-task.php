<?php
if (!defined('BASEPATH')) {
	require_once __DIR__ . './../Helpers/Helper.php';
	require_once (__DIR__ . './../../config/config.php');
	require_once (__DIR__ . './../Classes/Auth/Member.php');
	require_once (__DIR__ . './../Classes/TaskBook/Task.php');
    require_once (__DIR__ . './../Classes/SimpleValidator/Validator.php');
}

use \Аuth\Member;
use \TaskBook\Task;
use \SimpleValidator\Validator;

if (!isset($_SESSION)) {
    session_start();
}

$member = new Member();

$is_user_logged = $member->is_user_logged();
$is_admin = $member->is_admin();

if (!$is_user_logged && CAN_GUESTS_CREATE_TASKS == false) {
	$_SESSION["errorMessage"] = "You do not have permission for this action!";
    header("Location: /");
	exit();
}

if (! empty($_POST["new_task"]) || ! empty($_POST["new_task_exit"])) {
	
	$_SESSION['post-data'] = $_POST;

    $rules = [
        'user_name' => [
            'required',
            'max_length(200)',
        ],
        'email' => [
            'required',
            'email'
        ],
        'task' => [
            'required',
            'max_length(3000)'
        ]
    ];
    $fields = [
        'user_name' => 'Name',
        'email' => 'Email',
        'task' => 'Task'
    ];
    
    $validation_result = SimpleValidator\Validator::validate($_POST, $rules, $fields);
    if (!$validation_result->isSuccess()) {
        $_SESSION["errorMessage"] = implode('.<br>', $validation_result->getErrors('en'));
        header("Location: /add-task");
        exit();
    }
    
    //$readonly_field = ($is_user_logged && !$is_admin || $is_user_has_cookie) ? true : false;
    if ($is_user_logged && !$is_admin) {
		$username = $member->get_name();
    	$email = $member->get_email();
	} else {
		$username = filter_var($_POST["user_name"], FILTER_SANITIZE_STRING);
        $email = filter_var($_POST["email"], FILTER_SANITIZE_STRING);
	}
    $new_task = filter_var($_POST["task"], FILTER_SANITIZE_STRING);
    $created = time();
    
    $user_hash = null;
    if ($is_user_logged) {
        $poster_id = (!$is_admin || $username === $member->get_name() && $email === $member->get_email()) ? $member->get_user_id() : 0;
//    	$poster_id = $member->get_user_id();
    	$user_hash = null;
	} else {
		$poster_id = 0;
		if (CAN_GUESTS_EDIT_TASKS == true) {
		    if (isset($_COOKIE['task_book_user_hash'])) {
			    $user_hash = $_COOKIE['task_book_user_hash'];
		    } else {
			    $user_hash = md5(time());
			    setcookie('task_book_user_hash', $user_hash, 0, '/');
			    setcookie('task_book_username', $username, 0, '/');
			    setcookie('task_book_email', $email, 0, '/');
		    }
	    }
	}
    
    $task = new Task();
    $isTaskInserted = $task->addTask([$poster_id, $username, $email, $new_task, $created, $user_hash]);
    
    if (! $isTaskInserted) {
        $_SESSION["errorMessage"] = "An error occurred while saving your data to the database!
";
        header("Location: /add-task");
    } else {
    	$_SESSION["successMessage"] = "Task successfully created!";
    	if (($is_user_logged || CAN_GUESTS_CREATE_TASKS == true) && !empty($_POST["new_task"]) ) {
            header("Location: /edit-task?id={$isTaskInserted}");
		} else {
            header("Location: /");
		}
    	unset($_SESSION['post-data']);
	}
    exit(); 
}