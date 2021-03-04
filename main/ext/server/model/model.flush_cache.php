<?php
$sql = 'DELETE FROM `cache` WHERE `cache_id` = :id';
$params = [
	':id' => $row['cache_id']
];

$check = $db->query($sql, $params);
if($check == false){
	ob_start();
	debug_d($db->error);
	return ob_get_clean();
}

return $row['cache_id'];