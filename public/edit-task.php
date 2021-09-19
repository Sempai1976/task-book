<?php
if (!isset($_SESSION)) {
    session_start();
}

use \Аuth\Member;
use \TaskBook\Task;

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id < 1) {
	$_SESSION["errorMessage"] = "A task with this ID was not found!";
    header("Location: /");
	exit();
}

$member = new Member();
$task = new Task();

$is_user_logged = $member->is_user_logged();
$is_user_has_cookie = false;
if ($is_user_logged) 
{
    $username = $member->get_name();
    $is_admin = $member->is_admin();
    $user_id = $member->get_user_id();
} else {
    if (CAN_GUESTS_EDIT_TASKS == true && isset($_COOKIE['task_book_user_hash'])) {
	    $user_cookie = $_COOKIE['task_book_user_hash'];
	    if ($user_ids = $task->getTaskIdByHash($user_cookie)) {
			$ids = [];
            foreach($user_ids as $key => $val) {
	            $ids[] = $val['id'];
            }
            if (count($ids)>0) {
				$is_user_has_cookie = true;
			}
		}
	}
}

if (!$is_user_logged && !$is_user_has_cookie || $is_user_has_cookie && !in_array($id, $ids)) {
	$_SESSION["errorMessage"] = "You do not have permission for this action!";
    header("Location: /");
	exit();
}

//$task = new Task();
$cur_task = $task->getTaskById($id);

if (!$cur_task) {
	$_SESSION["errorMessage"] = "A task with this ID was not found!";
    header("Location: /");
	exit();
}

if ($is_user_logged && !$is_admin && $user_id != $cur_task[0]['poster_id']) {
	$_SESSION["errorMessage"] = "You do not have permission for this action!";
    header("Location: /");
	exit();
}

if (isset($_SESSION['post-data']) && isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] != '') {
	if (strstr($_SERVER['HTTP_REFERER'], 'edit-task') === false)
	    unset($_SESSION['post-data']);
}

//$readonly_field = ($is_user_logged && !$is_admin) ? true : false;
$readonly_field = ($is_user_logged && !$is_admin || !$is_user_logged) ? true : false;
?>

<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="Edit Task / We solve tasks">
        <meta name="author" content="">
        <title>Edit Task</title>
        <link rel="icon" href="<?php echo BASE_URL ?>public/images/favicon.ico">
        <link rel="canonical" href="<?php echo BASE_URL ?>edit-tsak">
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
       	<div class="page-header">
            <h2>Edit Task</h2>
        </div>
       	
        <form action="./../resources/Controllers/Edit-task.php" method="post" id="frmDelTask" onSubmit="return validate(this);">
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
                <label for="user_name">Your name</label>
                <input type="text" class="form-control" id="user_name" name="user_name" value="<?php if (isset($_SESSION['post-data']['user_name'])) { echo htmlspecialchars($_SESSION['post-data']['user_name']); } else { echo $cur_task[0]['user_name'];} ?>" required="required" <?php if ($readonly_field) echo 'readonly'; ?>>
            </div>
            <div class="form-group">
                <label for="email">Your email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php if (isset($_SESSION['post-data']['email'])) { echo htmlspecialchars($_SESSION['post-data']['email']); } else { echo $cur_task[0]['email'];} ?>" required="required" <?php if ($readonly_field) echo 'readonly'; ?>>
            </div>
            <div class="form-group">
                <label for="task">Task</label>
                <textarea class="form-control" id="task" name="task" rows="5" required="required"><?php if (isset($_SESSION['post-data']['task'])) { echo htmlspecialchars($_SESSION['post-data']['task']); } else { echo $cur_task[0]['task'];} ?></textarea>
            </div>
            <?php if ($is_user_logged && $is_admin) { ?>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="status" name="status" value="1" <?php if ($cur_task[0]['status'] == 1) echo ' checked="checked"' ?>>
                <label class="form-check-label" for="status">Make as solved</label>
            </div>
            <?php } else { ?>
                <?php if ($is_user_has_cookie) { ?>
                <input type="hidden" id="user_cookie" name="user_cookie" value="<?php echo $user_cookie; ?>" />
                <?php } ?>
            <?php } ?>
            <input type="hidden" id="taskID" name="taskID" value="<?php echo $cur_task[0]['id']; ?>" />
            <button type="submit" class="btn btn-success" name="edit_task" value="edit_task" style="min-width: 105px;
margin-right: 15px;">Save</button>
            <button type="submit" class="btn btn-primary" name="edit_task_exit" value="edit_task_exit">Save & Exit</button>
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