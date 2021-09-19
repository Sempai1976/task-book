<?php
if (!isset($_SESSION)) {
    session_start();
}

use \Аuth\Member;

$member = new Member();

$is_user_logged = $member->is_user_logged();
$is_admin = $member->is_admin();
if ($is_admin) {
	$username = $member->get_name();
}

if (($is_user_logged || ENABLE_REGISTRATION == false) && !$is_admin) {
    $_SESSION["errorMessage"] = "You do not have permission for this action!";
    header("Location: /");
	exit();
}

if (isset($_SESSION['post-data']) && isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] != '') {
	if (strstr($_SERVER['HTTP_REFERER'], 'register') === false)
	    unset($_SESSION['post-data']);
}
?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="Member Registration / We solve tasks">
        <meta name="author" content="">
        <title>Member Registration</title>
        <link rel="icon" href="<?php echo BASE_URL ?>public/images/favicon.ico">
        <link rel="canonical" href="<?php echo BASE_URL ?>add-task">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        <link href="https://fonts.googleapis.com/css?family=Roboto|Varela+Round" rel="stylesheet">
        <link href="<?php echo BASE_URL ?>public/css/style.css" rel="stylesheet" type="text/css" />
</head>
<body>

<!-- Start Modal HTML -->
<?php 
    if (!$is_user_logged) {
    	include(__DIR__."/login_modal.php");
	}
?>
<!-- End Modal HTML -->

     <header class="header">
         <nav class="navbar navbar-expand-md navbar-light bg-light fixed-topk" role="navigation">
             <a class="navbar-brand" href="/" role="banner">We solve tasks</a>
 
             <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsDefault" aria-controls="navbarsDefault" aria-expanded="false" aria-label="Switch navigation">
                 <span class="navbar-toggler-icon"></span>
             </button>
 
             <div class="collapse navbar-collapse" id="navbarsDefault">
                 <ul class="navbar-nav mr-auto">
                     <li class="nav-item">
                         <a class="nav-link" href="/">Home</a>
                     </li>
                     <li class="nav-item">
                         <a class="nav-link" href="/add-task">Add task</a>
                     </li>
                     <?php if (!$is_admin) { ?>
                     <li class="nav-item">
                         <!--a class="nav-link" href="/public/login.php">Login</a-->
                         <a href="#" class="nav-link" data-toggle="modal" data-target="#login-modal" onclick="loginModal.clear_form();">Login</a>
                     </li>
                     <?php } ?>
                     <li class="nav-item active">
                         <a class="nav-link" href="/register">Registration</a>
                     </li>
                     <?php if ($is_admin) { ?>
                     <li class="nav-item">
                         <div>
                             <div class="nav-login first"><?php echo $username; ?></div><div class="nav-login sep">|</div><div class="nav-login"><a class="nav-link" href="/../resources/Controllers/Logout.php">Logout</a></div>
                         </div>
                     </li>
                     <?php } ?>
                 </ul>
            </div>
         </nav>
    </header>

    <main role="main" class="container">
       	<div class="page-header">
            <h2>Member Registration</h2>
        </div>
       	
        <form action="./../resources/Controllers/Register.php" method="post" id="register_form" onSubmit="return validate(this);">
            <?php if(isset($_SESSION["successMessage"])) { ?>
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <?php  echo $_SESSION["successMessage"]; ?>
                </div>
            <?php unset($_SESSION["successMessage"]);} ?>

            <?php if(isset($_SESSION["errorMessage"])) { ?>
                <div class="alert alert-danger alert-dismissible">
                     <button type="button" class="close" data-dismiss="alert">&times;</button>
                     <?php  echo $_SESSION["errorMessage"]; ?>
                </div>
            <?php unset($_SESSION["errorMessage"]);} ?>

            <div class="form-group">
                <label for="user_name">Login</label>
                    <input type="text" class="form-control" id="user_name" name="user_name" value="<?php if (isset($_SESSION['post-data']['user_name'])) echo htmlspecialchars($_SESSION['post-data']['user_name']); ?>" required="required" placeholder="Enter login">
            </div>
            <div class="form-group">
                <label for="full_name">Full Name</label>
                    <input type="text" class="form-control" id="full_name" name="full_name" value="<?php if (isset($_SESSION['post-data']['full_name'])) echo htmlspecialchars($_SESSION['post-data']['full_name']); ?>" placeholder="Enter full name">
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php if (isset($_SESSION['post-data']['email'])) echo htmlspecialchars($_SESSION['post-data']['email']); ?>" required="required" placeholder="Enter email">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="text" class="form-control" id="password" name="password" value="<?php if (isset($_SESSION['post-data']['password'])) echo htmlspecialchars($_SESSION['post-data']['password']); ?>" required="required" placeholder="Enter password">
            </div>
            <div class="form-group">
                <label for="password_verify">Confirm password</label>
                <input type="password" class="form-control" id="password_verify" name="password_verify" value="<?php if (isset($_SESSION['post-data']['password_verify'])) echo htmlspecialchars($_SESSION['post-data']['password_verify']); ?>" required="required" placeholder="Re enter password">
            </div>
            <?php if ($is_admin) { ?>
            <div class="form-group">
                <label for="sel1">Select user group:</label>
                <select class="form-control" id="user_group" name="user_group">
                    <option value="<?php echo MEMBER_GROUP ?>" <?php if ($_SESSION['post-data']['user_group'] == MEMBER_GROUP) echo ' selected="selected"' ?>>Member</option>
                    <option value="<?php echo ADMIN_GROUP ?>" <?php if ($_SESSION['post-data']['user_group'] == ADMIN_GROUP) echo ' selected="selected"' ?>>Admin</option>
                </select>
            </div>
            <?php } ?>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="i_agree" name="i_agree" value="1" <?php if (isset($_SESSION['post-data']) && $_SESSION['post-data']['i_agree'] == 1) echo ' checked="checked"' ?>>
                <label class="form-check-label" for="i_agree">I agree to the collection, processing and storage of personal data</label>
            </div>
            <button type="submit" class="btn btn-success" name="register" value="register" style="min-width: 105px;">Submit</button>
        </form>

    </main>
   
    <footer class="footer">
        <div class="container">
            <span class="text-muted">We love to solve tasks!</span>
        </div>
    </footer>
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
    <script src="<?php echo BASE_URL ?>public/js/script.js" type="text/javascript"></script>
</body>
</html>