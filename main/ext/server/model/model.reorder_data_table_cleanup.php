<?php
$message = 'Setting AUTO_INCREMENT for `data`.<br>';
$sql = 'SELECT count(*) AS `count` FROM `data` WHERE 1';
$count = $db->t_query($sql, [])->fetch()['count'];

$sql = 'ALTER TABLE `data` AUTO_INCREMENT = '.($count + 1);
$check = $db->t_query($sql, []);
if($check == false){
	ob_start();
	debug_d($db->error);
	return ob_get_clean();
}
$message .= 'Clearing `cache` table.';
$sql = 'DELETE FROM `cache` WHERE 1; ALTER TABLE `cache` AUTO_INCREMENT = 1';
if($check == false){
	ob_start();
	debug_d($db->error);
	return ob_get_clean();
}
return $message;