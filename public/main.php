<?php
if (!isset($_SESSION)) {
    session_start();
}

use \Аuth\Member;
use \TaskBook\Task;

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
	if (CAN_GUESTS_EDIT_TASKS == true) {
        if (isset($_COOKIE['task_book_user_hash'])) {
	        $user_cookie = $_COOKIE['task_book_user_hash'];
	        if ($tasks_ids = $task->getTaskIdByHash($user_cookie)) {
			    $ids = [];
                foreach($tasks_ids as $key => $val) {
	                $ids[] = $val['id'];
                }
                if (count($ids)>0) {
				    $is_user_has_cookie = true;
			    }
		    }
	    }
	}
}

$page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;
$sort_by = isset($_GET['sort_by']) ? 'sort_by='.$_GET['sort_by'] : '';
$search_str = isset($_GET['search_str']) ? $_GET['search_str'] : '';

$order_by = 't.created DESC, t.updated DESC';
if ($sort_by != '')
{ 
	if ($sort_by == 'sort_by=user_name_asc') {
		$order_by = 't.user_name ASC';
	} else if($sort_by == 'sort_by=user_name_desc') {
		$order_by = 't.user_name DESC';
	} else if($sort_by == 'sort_by=email_asc') {
		$order_by = 't.email ASC';
	} else if($sort_by == 'sort_by=email_desc') {
		$order_by = 't.email DESC';
	} else if($sort_by == 'sort_by=created_asc') {
		$order_by = 't.created ASC';
	} else if($sort_by == 'sort_by=created_desc') {
		$order_by = 't.created DESC';
	} else if($sort_by == 'sort_by=updated_asc') {
		$order_by = 't.updated ASC';
	} else if($sort_by == 'sort_by=updated_desc') {
		$order_by = 't.updated DESC';
	} else if($sort_by == 'sort_by=status_asc') {
		$order_by = 't.status ASC';
	} else if($sort_by == 'sort_by=status_desc') {
		$order_by = 't.status DESC';
	}
}
$sort = ((!empty($sort_by)) ? '&'.$sort_by : '');
$search = ((!empty($search_str)) ? '&search_str='.$search_str : '');
$search_params = $sort . $search;

$where_like = "";
if (!empty($search_str)) {
    $where_like = "WHERE t.task LIKE '%{$search_str}%' OR t.user_name LIKE '%{$search_str}%' OR t.email LIKE '%{$search_str}%'";	
}

// Number of results to show on each page.
$max_items = ITEMS_ON_PAGE;

//$task = new Task();
$total_items = $task->getAllTaskCount($where_like);

$mysqli = $task->getConnection();

//if ($stmt = $mysqli->prepare('SELECT * FROM tasks '.$where_like.' ORDER BY '.$order_by.' LIMIT ?,?')) {
if ($stmt = $mysqli->prepare('SELECT t.*, u.group_id FROM tasks AS t LEFT JOIN users AS u ON t.poster_id = u.id AND t.poster_id > 0 '.$where_like.' ORDER BY '.$order_by.' LIMIT ?,?')) {
	$calc_page = ($page - 1) * $max_items;
	$stmt->bind_param('ii', $calc_page, $max_items);
	$stmt->execute();
	$result = $stmt->get_result();
?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="Home / We solve tasks">
        <meta name="author" content="">
        <title>We solve tasks</title>
        <link rel="icon" href="<?php echo BASE_URL ?>public/images/favicon.ico">
        <link rel="canonical" href="<?php echo BASE_URL ?>">
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

<div class="modal fade" id="confirm-part-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Confirm delete</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
				 <h4 class="text-center" style="color: red; padding-top: 5px;">Do you want to delete this task?</h4>
            </div>
            <div class="modal-footer">
                 <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                 <a class="btn btn-danger btn-ok">Delete</a>
            </div>
        </div>
    </div>
</div>
<!-- End Modal HTML -->

    <header class="header">
        <nav class="navbar navbar-expand-md navbar-light bg-light fixed-topk" role="navigation">
            <a class="navbar-brand" href="/" role="banner">We solve tasks</a>
 
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsDefault" aria-controls="navbarsDefault" aria-expanded="false" aria-label="Switch navigation">
                 <span class="navbar-toggler-icon"></span>
            </button>
 
            <div class="collapse navbar-collapse" id="navbarsDefault">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item active">
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

                <form id="search_form" class="form-inline" action="/" method="post">
                    <input type="search" id="search_task" name="search_task" class="form-control mr-sm-2" value="<?php if (!empty($search_str)) echo htmlspecialchars($search_str); ?>" placeholder="Search Task" required="required" maxlength="100">
                    <button class="btn btn-success" type="submit">Search</button>
                    <button id="clear_search" class="btn btn-danger" title="Clear search data" <?php if (!empty($search_str)) { ?>style="display: block;"<?php } ?>><i class="fa fa-remove"></i></button>
                </form>

                <!--div class="form-group-sort">
                    <select class="form-control" id="exampleFormControlSelect1" onchange="Tasks_sort_by(this, 'sel')">
                        <option value="sort_by=user_name_asc"<?php if ($sort_by == 'sort_by=user_name_asc') echo ' selected="selected"' ?>>Sort tasks by Username &uArr;</option>
                        <option value="sort_by=user_name_desc"<?php if ($sort_by == 'sort_by=user_name_desc') echo ' selected="selected"' ?>>Sort tasks by Username &dArr;</option>
                        <option value="sort_by=email_asc"<?php if ($sort_by == 'sort_by=email_asc') echo ' selected="selected"' ?>>Sort tasks by Email &uArr;</option>
                        <option value="sort_by=email_desc"<?php if ($sort_by == 'sort_by=email_desc') echo ' selected="selected"' ?>>Sort tasks by Email &dArr;</option>
                        <option value="sort_by=created_asc"<?php if ($sort_by == 'sort_by=created_asc') echo ' selected="selected"' ?>>Sort tasks by Created &uArr;</option>
                        <option value="sort_by=created_desc"<?php if ($sort_by == 'sort_by=created_desc') echo ' selected="selected"' ?>>Sort tasks by Created &dArr;</option>
                        <option value="sort_by=updated_asc"<?php if ($sort_by == 'sort_by=updated_asc') echo ' selected="selected"' ?>>Sort tasks by Updated &uArr;</option>
                        <option value="sort_by=updated_desc"<?php if ($sort_by == 'sort_by=updated_desc') echo ' selected="selected"' ?>>Sort tasks by Updated &dArr;</option>
                        <option value="sort_by=status_asc"<?php if ($sort_by == 'sort_by=status_asc') echo ' selected="selected"' ?>>Sort tasks by Status &uArr;</option>
                        <option value="sort_by=status_desc"<?php if ($sort_by == 'sort_by=status_desc') echo ' selected="selected"' ?>>Sort tasks by Status &dArr;</option>
                     </select>
                </div-->
        </nav>
    </header>

   <main role="main" class="container">
       	<div class="page-header">
       	    <div class="btn-toolbar pull-right" style="float: right; margin-top: 5px;">
                <h3><?php if (empty($search_str)) { ?>Total<?php } else {?>Found<?php } ?>: <?php echo $total_items; ?></h3>
            </div>
            <h2>Task List</h2>
        </div>
       	
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
        
        <?php if ($result->num_rows > 0) { ?>

       	<table id="tasks" class="table table-bordered table-hover table-responsive">
            <thead>
                <tr>
                    <th>#</th>
                    <th style="min-width: 100px;">Name 
                    <?php if($sort_by == 'sort_by=user_name_asc') { ?>
                        <span class="sort_arr active" name="sort_by=user_name_desc"><i class="fa fa-sort-alpha-asc" aria-hidden="true"></i></span>
                    <?php } elseif ($sort_by == 'sort_by=user_name_desc') { ?>
                        <span class="sort_arr active" name="sort_by=user_name_asc"><i class="fa fa-sort-alpha-desc" aria-hidden="true"></i></span>
                    <?php } else { ?>
                        <span class="sort_arr" name="sort_by=user_name_desc"><i class="fa fa-sort-alpha-asc" aria-hidden="true"></i></span>
                    <?php } ?>
                    </th>
                    <th style="min-width: 100px;">Email
                    <?php if($sort_by == 'sort_by=email_asc') { ?>
                        <span class="sort_arr active" name="sort_by=email_desc"><i class="fa fa-sort-alpha-asc" aria-hidden="true"></i></span>
                    <?php } elseif ($sort_by == 'sort_by=email_desc') { ?>
                        <span class="sort_arr active" name="sort_by=email_asc"><i class="fa fa-sort-alpha-desc" aria-hidden="true"></i></span>
                    <?php } else { ?>
                        <span class="sort_arr" name="sort_by=email_desc"><i class="fa fa-sort-alpha-asc" aria-hidden="true"></i></span>
                    <?php } ?>
                    </th>
                    <th>Task</th>
                    <th style="min-width: 110px;">Created
                    <?php if($sort_by == 'sort_by=created_asc') { ?>
                        <span class="sort_arr active" name="sort_by=created_desc"><i class="fa fa-sort-alpha-asc" aria-hidden="true"></i></span>
                    <?php } elseif ($sort_by == 'sort_by=created_desc') { ?>
                        <span class="sort_arr active" name="sort_by=created_asc"><i class="fa fa-sort-alpha-desc" aria-hidden="true"></i></span>
                    <?php } else { ?>
                        <span class="sort_arr" name="sort_by=created_<?php if ($sort_by != '' && $sort_by != 'created_asc' && $sort_by != 'created_desc') { ?>desc<?php } else { ?>asc<?php } ?>"><i class="fa fa-sort-alpha-<?php if ($sort_by != '' && $sort_by != 'created_asc' && $sort_by != 'created_desc') { ?>asc<?php } else { ?>desc<?php } ?>" aria-hidden="true"></i></span>
                    <?php } ?>
                    </th>
                    <th style="min-width: 115px;">Updated
                    <?php if($sort_by == 'sort_by=updated_asc') { ?>
                        <span class="sort_arr active" name="sort_by=updated_desc"><i class="fa fa-sort-alpha-asc" aria-hidden="true"></i></span>
                    <?php } elseif ($sort_by == 'sort_by=updated_desc') { ?>
                        <span class="sort_arr active" name="sort_by=updated_asc"><i class="fa fa-sort-alpha-desc" aria-hidden="true"></i></span>
                    <?php } else { ?>
                        <span class="sort_arr" name="sort_by=updated_<?php if ($sort_by != '' && $sort_by != 'updated_asc' && $sort_by != 'updated_desc') { ?>desc<?php } else { ?>asc<?php } ?>"><i class="fa fa-sort-alpha-<?php if ($sort_by != '' && $sort_by != 'updated_asc' && $sort_by != 'updated_desc') { ?>asc<?php } else { ?>desc<?php } ?>" aria-hidden="true"></i></span>
                    <?php } ?>
                    </th>
                    <th style="min-width: 100px;">Status 
                    <?php if ($sort_by == 'sort_by=status_asc') { ?>
                        <span class="sort_arr active" name="sort_by=status_desc"><i class="fa fa-sort-alpha-asc" aria-hidden="true"></i></span>
                    <?php } elseif ($sort_by == 'sort_by=status_desc') { ?>
                        <span class="sort_arr active" name="sort_by=status_asc"><i class="fa fa-sort-alpha-desc" aria-hidden="true"></i></span>
                    <?php } else { ?>
                        <span class="sort_arr" name="sort_by=status_desc"><i class="fa fa-sort-alpha-asc" aria-hidden="true"></i></span>
                    <?php } ?>
                    </th>
                    <?php if ($is_user_logged || $is_user_has_cookie) { ?>
                        <th>Action</th>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
            <?php 
                $i = isset($_GET['page']) ? $max_items * ($page-1)+1 : '1';
                while ($row = $result->fetch_assoc()) {
            ?>
	            <tr>
	                <td><?php echo $i; ?></td>
		            <td class="s_name">
					    <div class="username"><?php echo $row['user_name']; ?></div>
						<div class="avatar"><?php echo get_avatar($row['email'], 70); ?></div>
						<div style="margin-top: 10px;">
						    <?php if ($row['poster_id'] > 0) { ?>
							    <?php if ($row['group_id'] == '1') { ?>
								    Administrator
								<?php } else { ?>
							        Member
							    <?php } ?>
							<?php } else { ?>
							    Guest
							<?php } ?>
						</div>
					</td>
		            <td class="s_email"><?php echo $row['email']; ?></td>
		            <td class="s_task"><?php echo $row['task']; ?></td>
		            <td>
		                <?php echo get_adjusted_date($row['created']); ?>
		                <!--?php echo date('j.m.Y H:i', $row['created']); ?-->
		            </td>
		            <td>
		                <?php if ($row['updated'] > 0) echo get_adjusted_date($row['updated']); ?>
		                <!--?php if ($row['updated'] > 0) echo date('j.m.Y H:i', $row['updated']); ?-->
		            </td>
		            <td><?php if ($row['status'] == '1') { echo 'Solved'; } else { echo 'Not solved';} ?></td>
                    <?php if ($is_user_logged || $is_user_has_cookie) { ?>
                    <td>
                        <?php if ($is_user_logged && ($is_admin || $user_id == $row['poster_id']) || $is_user_has_cookie && in_array($row['id'], $ids)) { ?>
                        <a class="btn btn-primary" href="/edit-task?id=<?php echo $row['id']; ?>">Edit</a>
                        <?php } ?>
                        <?php if ($is_user_logged && $is_admin) { ?>
                        <a class="btn btn-danger" href="#" data-href="/../resources/Controllers/Delete-task.php?id=<?php echo $row['id']; ?>" data-toggle="modal" data-target="#confirm-part-delete">Delete</a>
                        <?php } ?>
                    </td>
                    <?php } ?>
	            </tr>
	            <?php $i++; } ?>
            </tbody>
        </table>
        <?php if ($is_user_logged || CAN_GUESTS_CREATE_TASKS == true) { ?>
            <a class="btn btn-success btn-block" href="/add-task" style="padding: 20px; font-size: 1.1rem;">Add task</a>
        <?php } ?>
  
        <?php if ($total_items > $max_items) { ?>
	    <ul class="pagination">
	        <li class="page-item disabled"><a class="page-link" href="#">Pages</a></li>
		    <?php if ($page > 1) { ?>
			    <li class="page-item"><a class="page-link" href="/?page=<?php echo $page-1 ?><?php echo $search_params ?>">Prev</a></li>
		    <?php } ?>
		    <?php if ($page > 3) { ?>
			    <li class="page-item"><a class="page-link" href="/?page=1<?php echo $search_params ?>">1</a></li>
			    <li class="page-item disabled"><a class="page-link" href="#">...</a></li>
		    <?php } ?>
		    <?php if ($page-2 > 0) { ?>
		        <li class="page-item"><a class="page-link" href="/?page=<?php echo $page-2 ?><?php echo $search_params ?>"><?php echo $page-2 ?></a></li>
		    <?php } ?>
		    <?php if ($page-1 > 0) { ?>
		        <li class="page-item"><a class="page-link" href="/?page=<?php echo $page-1 ?><?php echo $search_params ?>"><?php echo $page-1 ?></a></li>
		    <?php } ?>
		        <li class="page-item active"><a class="page-link" href="/?page=<?php echo $page ?><?php echo $search_params ?>"><?php echo $page ?></a></li>
		    <?php if ($page+1 < ceil($total_items / $max_items)+1) { ?>
		        <li class="page-item"><a class="page-link" href="/?page=<?php echo $page+1 ?><?php echo $search_params ?>"><?php echo $page+1 ?></a></li>
		    <?php } ?>
		    <?php if ($page+2 < ceil($total_items / $max_items)+1) { ?>
		        <li class="page-item"><a class="page-link" href="/?page=<?php echo $page+2 ?><?php echo $search_params ?>"><?php echo $page+2 ?></a></li>
		    <?php } ?>
		    <?php if ($page < ceil($total_items / $max_items)-2) { ?>
			    <li class="page-item disabled"><a class="page-link" href="#">...</a></li>
			    <li class="page-item"><a class="page-link" href="/?page=<?php echo ceil($total_items / $max_items) ?><?php echo $search_params ?>"><?php echo ceil($total_items / $max_items) ?></a></li>
		    <?php } ?>
		    <?php if ($page < ceil($total_items / $max_items)) { ?>
			    <li class="page-item"><a class="page-link" href="/?page=<?php echo $page+1 ?><?php echo $search_params ?>">Next</a></li>
		    <?php } ?>
	    </ul>
        <?php } ?>	
       	
       	<?php } else { ?>
        
        <div class="alert alert-warning alert-dismissible">
            No tasks found!
        </div>
        <?php if ($is_user_logged || CAN_GUESTS_CREATE_TASKS == true) { ?>
            <a class="btn btn-success btn-block" href="/add-task" style="padding: 20px; font-size: 1.1rem;">Add task</a>
        <?php } ?>

        <?php } ?>
   </main>
   
    <footer class="footer">
        <div class="container">
            <div class="pull-right">
                <span class="text-muted">Powered by PHP</span>
            </div>
            <span class="text-muted">We love to solve tasks!</span>
        </div>
    </footer>
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
    <script src="<?php echo BASE_URL ?>public/js/script.js" type="text/javascript"></script>
</body>
</html>

<?php
	$stmt->close();
} 
?>