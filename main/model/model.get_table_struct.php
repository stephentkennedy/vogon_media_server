<?php
/*
Name: Steph Kennedy
Date: 12/30/2019
Comment: This should obtain the table structure for tables we are searching from the database, so that we can pass it to code that needs it.
*/
$structure = [
	'columns' => [],
	'types' => []
];
$col_name = '';

foreach($tables as $table){
	if(count($tables) > 1){ //If we're looking at multiple tables, we need to be specific, otherwise using generic columns names won't hurt.
		$col_name = $table.'`.`';
	}
	$sql = 'SELECT COLUMN_NAME, DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = :db AND TABLE_NAME = :table';
	$params = [
		':db' => $db->settings['database']['name'],
		':table' => $table
	];
	$query = $db->t_query($sql, $params);
	if($query != false){
		
		$results = $query->fetchAll();
		foreach($results as $r){
			$structure['columns'][] = $col_name.$r['COLUMN_NAME'];
			$structure['types'][$col_name.$r['COLUMN_NAME']] = $r['DATA_TYPE'];
		}
	}else{
		return [
			'error' => $db->error
		];
	}
}
return $structure;