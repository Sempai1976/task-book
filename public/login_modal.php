<div id="login-modal" class="modal fade">
	<div class="modal-dialog modal-login">
		<div class="modal-content">
			<div class="modal-header">				
				<h4 class="modal-title">Member Login</h4>
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			</div>
			<div id="modal-body" class="modal-body">
			    <div class="alert alert-danger alert-dismissible login_error">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <div class="error-content"></div>
                </div>
                
                <form id="login_form" action="/../resources/Controllers/Login.php" method="post">
					<div class="form-group">
						<i class="fa fa-user"></i>
						<input type="text" class="form-control" name="user_name" placeholder="Username" required="required">
					</div>
					<div class="form-group">
						<i class="fa fa-lock"></i>
						<input type="password" class="form-control" name="password" placeholder="Password" required="required">					
					</div>
                    <div class="form-group form-check">
                        <input type="checkbox" class="form-check-input" name="remember" value="1">
                        <label class="form-check-label" for="remember">Remember me</label>
                    </div>
					<div class="form-group">
						<input type="submit" class="btn btn-primary btn-block btn-lg" name="login" value="Login">
					</div>
					<input type="hidden" name="login_modal" value="login_modal" />
				</form>				
				
			</div>
			<!--div class="modal-footer">
				<a href="#">Forgot Password?</a>
			</div-->
		</div>
	</div>
</div>