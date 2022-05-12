<?php
	$action = get_slug_part(1);
	if($action == 'new'){
		load_controller('header', ['title' => 'Create New Profile']);
		if(!isset($_POST['user_email'])){

		}else{
			$new_user = [
				'email' => $_POST['user_email'],
				'password' => '***',
				'role' => 0,
				'role_mods' => ''
			];
			load_model('add', $new_user, 'user');
			
			echo load_view('notify', [
				'class' => 'success',
				'message' => 'Successfully added user'
			]);
			
			
			
		}
		echo load_view('create_user', [], 'user');
		load_controller('footer');
	}else if($action == 'edit'){
		global $user;
		$user_email = get_slug_part(2);
		if(is_numeric($user_email)){
			$user_data = load_model('get', ['user_key' => $user_email], 'user');
		}else{
			$user_data = load_model('get_by_email', ['email' => $user_email], 'user');
		}
		
		if(isset($_POST['user_email']) && $user_data['user_key'] == $user_data['user_key']){
			debug_d('hello');
			die();
			$new_data = [
				'user_key' => $_POST['user_key'],
				'user_email' => $_POST['user_email'],
				'user_pass' => '***'
			];
			load_model('update', $new_data, 'user');
			header('Location:' . build_slug('', [], 'user'));
			die();
		}
		if(isset($_POST['host']) && $user['user_key'] == $user_data['user_key'] && $_POST['email_password'] == $_POST['email_password_confirm']){
			$encrypt = $_POST['email_password'];
			$key = $_POST['user'];
			$encrypted = load_model('encrypt', ['encrypt' => $encrypt, 'key' => $key]);
			$new_data = ['content' => [
				'host' => $_POST['host'],
				'port' => $_POST['port'],
				'user' => $_POST['user'],
				'password' => $encrypted['encrypted'],
				'iv' => $encrypted['iv'],
				'encrypted' => true
			]];
			load_model('update_email', $new_data, 'user');
			header('Location:' . build_slug('', [], 'user'));
			die();
		}
		load_controller('header', ['title' => 'Edit Profile']);
		if(isset($_POST['user_email'])){
			echo load_view('notify', ['class' => 'error', 'message' => 'You can&#39;t modify someone else&#39;s account']);
		}
		
		echo load_view('edit_user', $user_data, 'user');
		load_controller('footer');
	}else if($action == 'remove'){
		$user_email = get_slug_part(2);
		$user_data = load_model('get', ['user_key' => $user_email], 'user');
		$confirm = get_slug_part(3);
		if(empty($confirm)){
			load_controller('header', ['title' => 'Confirm Profile Removal']);
			echo load_view('remove_confirm', $user_data, 'user');
			load_controller('footer');
		}else{
			load_model('remove', $user_data, 'user');
			redirect(build_slug('', [], 'user'));
		}
	}else if($action == 'logout'){
		load_controller('logout');
	}else{
		load_controller('header', ['title' => 'Profiles']);
		if(empty($_REQUEST['action']) || $_REQUEST['action'] != 'search' || empty($_REQUEST['search'])){
			$users = load_model('search', [
				'search_query' => '',
				'tables' => ['user'],
				'links' => []
			]);
			if($users === false){
				echo load_view('notify', [
					'class' => 'error',
					'message' => 'We were unable to find any results'
				]);
				$users = [];
			}
		}else{
			$users = load_model('search', [
				'search_query' => $_REQUEST['search'],
				'tables' => ['user'],
				'links' => []
			]);
			if($users === false){
				echo load_view('notify', [
					'class' => 'error',
					'message' => 'We were unable to find any results'
				]);
				$users = [];
			}
		}
		echo load_view('users', ['users' => $users], 'user');
		load_controller('footer');
	}
