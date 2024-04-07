<?php

	//$menu = json_decode($_SESSION['header_menu'], true);
	
	//$sql = '';
	global $user;
	
	if(empty($title)){
		$title = '';
	}
	$menu = json_decode($_SESSION['header_menu'], true);
	if($menu != false){
		if(!empty($user) && !empty($user['user_role']) && $user['user_role'] != 0){
			$sql = 'SELECT * FROM data WHERE data_name = "menu" AND data_parent = :role';
			$params = [
				':role' => $user['user_role']
			];
			$query = $db->t_query($sql, $params);
			$check = $query->fetch();
			if($check){
				$menu_mod = json_decode($check['data_content'], true);
				foreach($menu as $k => $v){
					if(!in_array($k, $menu_mod)){
						unset($menu[$k]);
					}
				}
			}
		}
		$print_menu = '';
		foreach($menu as $route => $line){
			$print_menu .= $line;
		}
	}else{
		$print_menu = $_SESSION['header_menu'];
	}

	$default_tags = [
		'<script type="text/javascript" src="'.build_slug('/js/jquery.min.js').'"></script>',
		'<script type="text/javascript" src="'.build_slug('/js/jquery-ui.min.js').'"></script>',
		'<script type="text/javascript" src="'.build_slug('/js/aPopup.js').'?ver='.time().'"></script>',
		'<script type="text/javascript" src="'.build_slug('/js/lazy.js').'"></script>',
		'<link rel="stylesheet" href="'.build_slug('/js/jquery-ui.min.css').'" type="text/css">',
		'<link rel="stylesheet" href="'.build_slug('/css/layout.css').'?ver='.time().'" type="text/css">'
	];

	$to_return = array_merge($default_tags, $header_tags);

	return $header_data = [
		'stylesheet' => $_SESSION['css'],
		'logo' => '',
		'logo_title' => '',
		'logo_alt' => '',
		'head_tags' => $to_return,
		'header_nav' => $print_menu,
		'title' => $title
	];
?>