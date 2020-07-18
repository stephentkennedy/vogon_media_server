<?php

	//Hard coded until an interface can be built.
	$tables = [
		'data' => false,
		'data_meta' => false,
		'route' => true,
		'var' => true,
		'user' => false,
		'session' => false,
	];
	$table_struct = [];
	$table_data = [];
	foreach($tables as $table => $data){
		if($data == true){
			$sql = 'SELECT * FROM `'.$table.'`';
			$query = $db->query($sql);
			$t_data = $query->fetchAll();
			$sql = 'DESCRIBE `'.$table.'`';
			$query = $db->query($sql);
			$c_data = $query->fetchAll();
			$primary = '';
			foreach($c_data as $c){
				if($c['Key'] == 'PRI'){
					$primary = $c['Field'];
					break;
				}
			}
			//Remove primary keys.
			foreach($t_data as $key => $d){
				unset($t_data[$key][$primary]);
			}
			$table_data[$table] = $t_data;
		}else{
			$table_data[$table] = [];
		}
		$sql = 'SHOW CREATE TABLE `'.$table.'`';
		$query = $db->query($sql);
		$t_data = $query->fetch();
		$table_struct[$table] = $t_data['Create Table'];
	}
	return [
		'tables' => $tables,
		'struct' => $table_struct,
		'records' => $table_data
	];