<?php
$message = 'Setting AUTO_INCREMENT for `data_meta`.<br>';
$sql = 'SELECT count(*) AS `count` FROM `data_meta` WHERE 1';
$count = $db->t_query($sql, [])->fetch()['count'];

$sql = 'ALTER TABLE `data_meta` AUTO_INCREMENT = '.($count + 1);
$check = $db->t_query($sql, []);
if($check == false){
	ob_start();
	debug_d($db->error);
	return ob_get_clean();
}
return $message;