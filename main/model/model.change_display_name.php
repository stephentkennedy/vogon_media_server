<?php
	if(!isset($id) || !isset($display_name)){
		$id = $_GET['id'];
		$sql = 'SELECT * FROM `route` WHERE `route_id` = :id';
		$params = [
			':id' => $id
		];
		$query = $db->query($sql, $params);
		$result = $query->fetch();
		return [
			'current_value' => $result['nav_display'],
			'id' => $id
		];
	}else{
		$sql = 'UPDATE `route` SET `nav_display` = :name WHERE `route_id` = :id';
		$params = [
			':name' => $display_name,
			':id' => $id
		];
		$db->query($sql, $params);
	}
?>