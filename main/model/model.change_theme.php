<?php
	$sql = 'UPDATE var SET var_content = :content WHERE var_name = "css"';
	$params = [
		':content' => 'css/'.$theme
	];
	$db->t_query($sql, $params);
?>