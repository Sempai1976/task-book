<?php
if (!defined('BASEPATH')) {
	require_once __DIR__ . './../Helpers/Helper.php';
	require_once (__DIR__ . './../../config/config.php');
	require_once (__DIR__ . './../Classes/Auth/Member.php');
    require_once (__DIR__ . './../Classes/TaskBook/Task.php');
}

use \Аuth\Member;
use \TaskBook\Task;

if (!isset($_SESSION)) {
    session_start();
}

$member = new Member();

if (!$member->is_user_logged()) {
	$_SESSION["errorMessage"] = "You do not have permission for this action!";
    header("Location: /");
	exit();
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id < 1) {
	$_SESSION["errorMessage"] = "A task with this ID was not found!";
    header("Location: /");
	exit();
}

$task = new Task();
$task->deleteTask($id);
    
$_SESSION["successMessage"] = "Task deleted!";
header("Location: /");
exit();