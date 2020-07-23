<header><h1>Edit User</h1></header>
<form method="post">
	<input type="hidden" name="user_key" value="<?php echo $user_key; ?>">
	<label for="user_email">Email</label>
	<input type="email" id="user_email" name="user_email" placeholder="example@test.com" value="<?php echo $user_email; ?>">
	<label for="user_password">Password</label>
	<input type="password" id="user_password" name="user_password" placeholder="Password" value="<?php echo '*******'; ?>">
	<label for="user_password_confirm">Confirm Password</label>
	<input type="password" id="user_password_confirm" name="user_password_confirm" placeholder="Password" value="<?php echo '*******'; ?>">
	<button id="submit" type="submit">Edit</button>
</form><br>
<form method="post">
	<input type="hidden" name="action" value="edit">
	<label for="host">Email Host</label>
	<input type="text" name="host" id="host" value="<?php echo $host; ?>">
	<label for="port">Email Port</label>
	<input type="text" name="port" id="port" value="<?php echo $port; ?>">
	<label for="user">Email User</label>
	<input type="text" name="user" id="user" value="<?php echo $user; ?>">
	<label for="email_password">Email Password</label>
	<input type="password" name="email_password" id="email_password" value="<?php echo $password; ?>">
	<label for="email_password_confirm">Confirm Email Password</label>
	<input type="password" name="email_password_confirm" id="email_password_confirm" value="<?php echo $password; ?>">
	<button id="submit2" type="submit">Edit</button>
</form>
<script type="text/javascript">
	$(document).ready(function(){
		$('#user_password').keyup(function(){
			passcheck();
		});
		$('#email_password').keyup(function(){
			passcheck();
		});
		$('#user_password_confirm').keyup(function(){
			passcheck();
		});
		$('#email_password_confirm').keyup(function(){
			passcheck();
		});
	});
	function passcheck(){
		var pass = $('#user_password').val();
		var pass_check = $('#user_password_confirm').val();
		var pass2 = $('#email_password').val();
		var pass2_check = $('#email_password_confirm').val();
		if(pass !== pass_check){
			$('#user_password_confirm').addClass('issue').attr('title', 'Passwords Must Match');
			$('#submit').attr('disabled', true);
		}else{
			$('#user_password_confirm').removeClass('issue').attr('title', '');
			$('#submit').attr('disabled', false);
		}
		if(pass2 !== pass2_check){
			$('#password_confirm').addClass('issue').attr('title', 'Passwords Must Match');
			$('#submit2').attr('disabled', true);
		}else{
			$('#password_confirm').removeClass('issue').attr('title', '');
			$('#submit2').attr('disabled', false);
		}
	}
</script>