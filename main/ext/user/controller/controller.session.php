<?php
	if(isset($_POST['email']) && isset($_POST['password'])){
		$password_data = [
			'email' => $_POST['email'],
			'password' => $_POST['password']
		];
		$check = load_model('compare_password', $password_data, 'user');
		if($check['correct'] == true){			
			load_model('start_session', ['user_key' => $check['user_key']], 'user');
			redirect($_SERVER['REQUEST_URI']);
		}else{
			echo load_view('login', [], 'user');
			die(); //Die here so that no additional routing will be done.
		}
	}else{
		$session_info = load_model('resume_session', [], 'user');
		if($session_info['session'] == false 
		|| (!empty($session_info['reauth']) 
			&& $session_info['reauth'] == true)
		){
			echo load_view('login', [], 'user');
			die(); //Die here so that no additional routing will be done.
		}
	}
?>