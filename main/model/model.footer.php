<?php

	global $user;
	$menu = json_decode($_SESSION['footer_menu'], true);
	if($menu != false){
		if(!empty($user) && $user['user_role'] != 0){
			$sql = 'SELECT * FROM data WHERE data_name = "menu" AND data_parent = :role';
			$params = [
				':role' => $user['user_role']
			];
			$query = $db->query($sql, $params);
			$menu_mod = json_decode($query->fetch()['data_content'], true);
			foreach($menu as $k => $v){
				if(!in_array($k, $menu_mod)){
					unset($menu[$k]);
				}
			}
		}
		$print_menu = '';
		foreach($menu as $line){
			$print_menu .= $line;
		}
	}else{
		$print_menu = $_SESSION['footer_menu'];
	}

	return $footer_data = [
		'footer_nav' => $print_menu
	];
?>