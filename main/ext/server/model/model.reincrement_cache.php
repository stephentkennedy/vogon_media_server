<?php
$sql = 'SELECT count(`cache_id`) as `count` FROM `cache` WHERE 1';
$query = $db->t_query($sql, []);
$count = $query->fetch()['count'];

$sql = 'ALTER TABLE `cache` AUTO_INCREMENT = '.($count + 1);
$check = $db->t_query($sql, []);
if($check == false){
	ob_start();
	debug_d($db->error);
	return ob_get_clean();
}else{
	return 'Reincremented Cache table.';
}