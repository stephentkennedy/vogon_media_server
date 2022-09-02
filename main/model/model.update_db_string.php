<?php
$sql = 'UPDATE `'.$table.'` SET `'.$column.'` = :value WHERE `'.$id_column.'` = :id';

$params = [
	':value' => $value,
	':id' => $id
];

$db->t_query($sq, $params);
?>