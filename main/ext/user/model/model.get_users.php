<?php

	$sql = 'SELECT * FROM `user`';
	$query = $db->t_query($sql);
	$users = $query->fetchAll();
	return $users;