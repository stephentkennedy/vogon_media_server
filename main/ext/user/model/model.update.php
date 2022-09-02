<?php
/*
	Name: Steph Kennedy
	Date: 11/21/19
	Comment: This model is a little more complicated than our add because it's intended to accepts variable data. The idea being that we can use the same model for any kind of update and multiple forms.
*/

	//Get our default values
	$user_data = load_model('get', ['user_key' => $user_key], 'user');
	
	//Don't want to override our salt when we're updating the password;
	if(isset($data['user_salt'])){
		unset($data['user_salt']);
	}
	
	//Override with provided data
	foreach($data as $key => $value){
		
		//Take user passwords and hash them before adding them to the database
		if($key == 'user_pass' && $value != '*******'){
			$user_salt = load_model('generate_salt', [], 'user');
			$user_pass = load_model('hash_password', [
				'password' => $user_pass,
				'salt' => $user_salt
			], 'user');
			$user_data['user_salt'] = $user_salt;
			$user_data['user_pass'] = $user_pass;
			$value = $user_pass;
		}
		
		if(isset($user_data[$key]) && $key != 'user_salt' && $key != 'user_pass'){
			$user_data[$key] = $value;
		}
	}
	
	foreach($user_data as $key => $value){
		if(is_numeric($key)){
			unset($user_data[$key]);
		}
	}
	
	//Remove the system controlled values.
	unset($user_data['user_key']);
	unset($user_data['create_date']);
	unset($user_data['last_edit']);
	
	//Build query and params
	$params = [];
	$updates = [];
	$sql = 'UPDATE `user` SET ';
	foreach($user_data as $key => $value){
		$params[':'.$key] = $value;
		$updates[] = '`'.$key . '` = :'.$key;
	}
	$sql .= implode(', ', $updates) . ' WHERE user_key = :id';
	$params[':id'] = $user_key;
	//run query and return result
	return $db->t_query($sql, $params);
?>