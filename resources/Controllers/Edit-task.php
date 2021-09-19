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

$is_user_has_cookie = false;
if (!$is_user_logged && CAN_GUESTS_EDIT_TASKS == true) {
    if (isset($_COOKIE['task_book_user_hash']) && !empty($_POST['user_cookie']) && $_POST['user_cookie'] == $_COOKIE['task_book_user_hash']) {
        $is_user_has_cookie = true;
    }
}

if (!$is_user_logged && !$is_user_has_cookie) {
	$_SESSION["errorMessage"] = "You do not have permission for this action!";
    header("Location: /");
	exit();
}

if (! empty($_POST["edit_task"]) || ! empty($_POST["edit_task_exit"])) {
	
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
    
    $task_id = intval($_POST["taskID"]);
    
    $validation_result = SimpleValidator\Validator::validate($_POST, $rules, $fields);
    if (!$validation_result->isSuccess()) {
        $_SESSION["errorMessage"] = implode('.<br>', $validation_result->getErrors('en'));
        header("Location: /edit-task?id={$task_id}");
        echo $task_id;
        exit();
    }

    $task = new Task();
    if ($is_user_logged) {
        $cur_task = $task->getTaskById($task_id);
	}

    if ($is_user_logged && !$is_admin) {
		$username = $member->get_name();
    	$email = $member->get_email();
    	$status = $cur_task[0]['status'];
	} elseif (!$is_user_logged) {
		$username = $_COOKIE['task_book_username'];
		$email = $_COOKIE['task_book_email'];
		$status = $cur_task[0]['status'];
	}
    if ($is_admin) {
    	$username = filter_var($_POST["user_name"], FILTER_SANITIZE_STRING);
        $email = filter_var($_POST["email"], FILTER_SANITIZE_STRING);
        $status = ($_POST["status"]) ? 1 : 0;
	}
	$edit_task = filter_var($_POST["task"], FILTER_SANITIZE_STRING);
    $updated = time();

    $isTaskUpdated = $task->updateTask([$username, $email, $edit_task, $updated, $status, $task_id]);
    
    if (! $isTaskUpdated) {
        $_SESSION["errorMessage"] = "An error occurred while saving your data to the database!
";
        header("Location: /edit-task?id={$task_id}");
    } else {
    	$_SESSION["successMessage"] = "Task successfully edited!";
    	if (! empty($_POST["edit_task"])) {
            header("Location: /edit-task?id={$task_id}");
		} else {
            header("Location: /");
		}
    	unset($_SESSION['post-data']);
	}
    exit(); 
}