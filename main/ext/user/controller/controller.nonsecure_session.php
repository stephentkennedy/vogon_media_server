<?php
	if(isset($_POST['user_key'])){
		
		//Move this DB logic to a model.
		$sql = 'SELECT * FROM user WHERE user_key = :id';
		$params = [
			':id' => $_POST['user_key']
		];
		$query = $db->query($sql, $params);
		if($query == false){
			$users = load_model('get_users', [], 'user');
			load_controller('header', ['view' => 'mini']);
			echo load_view('nonsecure_login', ['users' => $users], 'user');
			load_controller('footer', ['view' => 'mini']);
			die(); //Die here so that no additional routing will be done.
		}else{
			$check = $query->fetch();
			if(empty($check)){
				$users = load_model('get_users', [], 'user');
				load_controller('header', ['view' => 'mini']);
				echo load_view('nonsecure_login', ['users' => $users], 'user');
				load_controller('footer', ['view' => 'mini']);
				die(); //Die here so that no additional routing will be done.
			}
			global $user;
			$user = $check;
			load_model('start_session', ['user_key' => $check['user_key']], 'user');
		}
	}else{
		$session_info = load_model('resume_session', [], 'user');
		if($session_info['session'] == false 
		|| (!empty($session_info['reauth']) 
			&& $session_info['reauth'] == true)
		){
			$users = load_model('get_users', [], 'user');
			load_controller('header', ['view' => 'mini', 'title' => 'Choose Profile']);
			echo load_view('nonsecure_login', ['users' => $users], 'user');
			load_controller('footer', ['view' => 'mini']);
			die(); //Die here so that no additional routing will be done.
		}
	}
?>