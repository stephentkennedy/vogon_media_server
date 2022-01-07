<?php

	$sql = 'SELECT * FROM `user`';
	$query = $db->query($sql);
	$users = $query->fetchAll();
	return $users;