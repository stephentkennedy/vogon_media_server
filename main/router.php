<?php
$request = explode('?', $_SERVER['REQUEST_URI'])[0];
$trim = strlen(URI);
if(substr($request, 0, $trim) == URI){
	$request = substr($request, $trim);
}
$request = explode('/', $request);
$slug = @$request[1];
$sql = 'SELECT * FROM route WHERE route_slug = :slug';
$params = [
	':slug' => $slug
];
$route = $db->t_query($sql, $params);
if($db->error == false){
	$route = $route->fetchAll();
	if(!empty($route)){
		$route = $route[0];
	}
	if(empty($_GET['route']) && !empty($route)){
		if(empty($route['route_ext']) && !empty($route['route_controller'])){
			$_ENV['controller'] = $route['route_controller'];
			$_ENV['ext'] = '';
			load_controller($route['route_controller'], ['method' => 'route']);
		}else if(!empty($route['route_controller'])){
			$_ENV['controller'] = $route['route_controller'];
			$_ENV['ext'] = $route['route_ext'];
			load_controller($route['route_controller'], ['method' => 'route'], $route['route_ext']);
		}else{
			load_controller('404');
		}
	}else if(!empty($route) && !empty($_GET['route'])){
		if(file_exists(ROOT . DIRECTORY_SEPARATOR . 'main' . DIRECTORY_SEPARATOR . 'ext' . DIRECTORY_SEPARATOR . $route['route_ext'] . DIRECTORY_SEPARATOR . 'controller' . DIRECTORY_SEPARATOR . 'controller.' . $_GET['route'] . '.php')){
			load_controller($_GET['route'], ['method' => 'GET'], $route['route_ext']);
		}else{
			load_controller('404');
		}
	}else{
		load_controller('404');
	}
}else{
	echo $db->error->getMessage();
}