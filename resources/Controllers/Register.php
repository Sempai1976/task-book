<?php
if (!defined('BASEPATH')) {
	require_once __DIR__ . './../Helpers/Helper.php';
	require_once (__DIR__ . './../../config/config.php');
	require_once (__DIR__ . './../Classes/Auth/Member.php');
	require_once (__DIR__ . './../Classes/SimpleValidator/Validator.php');
}

use \Аuth\Member;
use \SimpleValidator\Validator;

if (!isset($_SESSION)) {
    session_start();
}

$member = new Member();
$is_user_logged = $member->is_user_logged();
$is_admin = $member->is_admin();

if (($is_user_logged || ENABLE_REGISTRATION == false) && !$is_admin) {
	$_SESSION["errorMessage"] = "You do not have permission for this action!";
    header("Location: /");
	exit();
}

if (! empty($_POST["register"])) {
	
	$_SESSION['post-data'] = $_POST;
	
	$user_name = filter_var($_POST["user_name"], FILTER_SANITIZE_STRING);
	$email = filter_var($_POST["email"], FILTER_SANITIZE_STRING);

    $rules = [
        'user_name' => [
            'required',
            'alpha_numeric',
            'max_length(20)',
            'name_exists' => function($user_name) {
            	 $mem = new Member();
                 $result = $mem->isDupeField("user_name", "s", $user_name);
                 return ($result) ? false : true;
            }
        ],
        'full_name' => [
            'max_length(200)'
        ],
        'email' => [
            'required',
            'email',
            'max_length(255)',
            'email_exists' => function($email) {
                 $mem = new Member();
                 $result = $mem->isDupeField("email", "s", $email);
                 return ($result) ? false : true;
            }
        ],
        'password' => [
            'required',
            'equals(:password_verify)'
        ],
        'password_verify' => [
            'required'
        ],
        'i_agree' => [
            'required'
        ]
    ];
    $fields = [
        'user_name' => 'Login',
        'full_name' => 'Full name',
        'email' => 'Email',
        'password' => 'Password',
        'password_verify' => 'Confirm password',
        'i_agree' => 'I agree'
    ];
    
    $validation_result = SimpleValidator\Validator::validate($_POST, $rules, $fields);
    if (!$validation_result->isSuccess()) {
        $_SESSION["errorMessage"] = implode('.<br>', $validation_result->getErrors('en'));
        header("Location: /register");
        exit();
    }

    $full_name = filter_var($_POST["full_name"], FILTER_SANITIZE_STRING);
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $group_id = ($is_admin) ? $_POST["user_group"] : MEMBER_GROUP;

    $memberInserted = $member->createMember([$group_id, $user_name, $full_name, $email, $password]);
    
    if (! $memberInserted) {
        $_SESSION["errorMessage"] = "An error occurred while saving your data to the database!
";
        header("Location: /register");
    } else {
    	if ($is_admin) {
			$_SESSION["successMessage"] = "Member successfully registered! (login: ".$user_name.", password: ".$_POST["password"].").";
		} else {
			$_SESSION["successMessage"] = "You have successfully registered! Now you can login with your username and password.";
		}
        header("Location: /");
    	unset($_SESSION['post-data']);
	}
    exit(); 
}