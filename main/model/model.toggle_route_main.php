<?php

$sql = 'SELECT * FROM route WHERE route_id = :id';
$params = [
	':id' => $id
];
$query = $db->query($sql, $params);
$route = $query->fetch();
if(!empty($route)){
	if($route['ext_primary'] == 0){
		$params[':ext'] = 1;
	}else{
		$params[':ext'] = 0;
	}
	$sql = 'UPDATE route SET ext_primary = :ext WHERE route_id = :id';
	$db->query($sql, $params);
}