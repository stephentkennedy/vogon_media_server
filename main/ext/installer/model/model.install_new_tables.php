<?php
foreach($new as $table){
	$create = $db_tables[$table]['create'];
	$db->t_query($create);
	if(count($db_tables[$table]['records']) > 1){
		foreach($db_tables[$table]['records'] as $r){
			$columns = array_keys($r);
			$sql = 'INSERT INTO `'.$table.'` (`'.implode('`, `', $columns).'`) VALUES (:'.implode(', :', $columns).')';
			$params = [];
			foreach($columns as $c){
				$params[':'.$c] = $r[$c];
			}
			$db->t_query($sql, $params);
		}
	}
}