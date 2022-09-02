<?php
	$sql = 'DELETE FROM `route` WHERE `route_id` = :id';
	$params = [':id' => $id];
	$query = $db->t_query($sql, $params);
?>