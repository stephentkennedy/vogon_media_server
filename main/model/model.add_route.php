<?php
	$sql = 'INSERT INTO route (route_controller, route_ext, route_slug) VALUES (:cont, :ext, :slug)';
	$params = [
		':cont' => $controller,
		':ext' => $ext,
		':slug' => $slug
	];
	$db->query($sql, $params);
?>