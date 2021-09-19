<?php 
if (!defined('BASEPATH')) {
	require_once __DIR__ . './../Helpers/Helper.php';
	require_once (__DIR__ . './../../config/config.php');
	require_once (__DIR__ . './../Classes/Autologin/Sessions.php');
}

use \Autologin\Sessions;

if (!isset($_SESSION)) {
    session_start();
}

$autologin = new Sessions();
$autologin->delete_autologin_cookie();

session_destroy();
header("Location:".$_SERVER['HTTP_REFERER']);