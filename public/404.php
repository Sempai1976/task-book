<?php
if (!isset($_SESSION)) {
    session_start();
}
    
use \Аuth\Member;

$member = new Member();

$is_user_logged = $member->is_user_logged();
if ($is_user_logged) 
{
    $username = $member->get_name();
}
?>

<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="Error 404 / We solve tasks">
        <meta name="author" content="">
        <title>Error 404</title>
        <link rel="icon" href="<?php echo BASE_URL ?>public/images/favicon.ico">
        <link rel="canonical" href="<?php echo BASE_URL ?>404">
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
                    <?php if ($is_user_logged || CAN_GUESTS_CREATE_TASKS == true) { ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/add-task">Add task</a>
                    </li>
                    <?php } ?>
                     <?php if (!$is_user_logged) { ?>
                     <li class="nav-item">
                         <!--a class="nav-link" href="/public/login.php">Login</a-->
                         <a href="#" class="nav-link" data-toggle="modal" data-target="#login-modal" onclick="loginModal.clear_form();">Login</a>
                     </li>
                     <?php if (ENABLE_REGISTRATION == true) { ?>
                     <li class="nav-item">
                         <a class="nav-link" href="/register">Registration</a>
                     </li>
                     <?php } ?>
                     <?php } else { ?>
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
   
       <?php if(isset($_SESSION["successMessage"])) { ?>
           <div class="alert alert-success alert-dismissible">
               <button type="button" class="close" data-dismiss="alert">&times;</button>
               <?php  echo $_SESSION["successMessage"]; ?>
           </div>
       <?php unset($_SESSION["successMessage"]);} ?>

       <div class="page-wrap d-flex flex-row align-items-center" style="min-height: 50vh;">
           <div class="container">
               <div class="row justify-content-center">
                   <div class="col-md-12 text-center">
                       <span class="display-1 d-block">404</span>
                       <div class="mb-4 lead">The page you are looking for was not found.</div>
                       <a href="/" class="btn btn-link">Back to Home</a>
                   </div>
               </div>
           </div>
       </div>
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