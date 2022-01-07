<?php
	$sql = 'DELETE FROM `route` WHERE `route_id` = :id';
	$params = [':id' => $id];
	$query = $db->query($sql, $params);
?>