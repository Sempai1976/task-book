<?php
if (!defined('BASEPATH')) {
	require_once __DIR__ . './../Helpers/Helper.php';
	require_once (__DIR__ . './../../config/config.php');
	require_once (__DIR__ . './../Classes/Auth/Member.php');
	require_once (__DIR__ . './../Classes/Autologin/Sessions.php');
}
use \Аuth\Member;
use \Autologin\Sessions;

if (! empty($_POST["login_modal"])) {
    if (!isset($_SESSION)) {
        session_start();
    }

    $username = filter_var($_POST["user_name"], FILTER_SANITIZE_STRING);
    $password = filter_var($_POST["password"], FILTER_SANITIZE_STRING);
    $remember = ($_POST["remember"]) ? 1 : 0;
    
    $member = new Member();
    $result = $member->login($username, $password);
    if ($result) {
    	$autologin = new Sessions();
    	$autologin->set_session($result);
    	if ($remember == 1) {
            $autologin->set_autologin_cookie();
		}
    	$member->set_userdata();

    	$_SESSION["successMessage"] = "Welcome <b>".$result[0]['full_name']."</b>, you have successfully logged in!";
		echo json_encode(array('answer'=>'success'));
        die;
    } else {
        echo json_encode(array('answer'=>'error', 'error'=>'Incorrect username or password'));
        die;
	} 
}
