<?php

	//Hard coded until an interface can be built.
	if(empty($tables)){
		$tables = [
			'data' => false,
			'data_meta' => false,
			'route' => true,
			'var' => true,
			'user' => false,
			'user_meta' => false,
			'session' => false,
			'contact' => false,
			'contact_meta' => false,
			'touch' => false,
			'stats' => false,
			'message' => false,
			'role' => true,
			'campaign' => false,
			'website' => false,
			'hour' => false,
			'cron' => false,
			'email' => false,
			//'history' => false,
			//'cache' => false,
		];
	}
	$table_struct = [];
	$table_data = [];
	foreach($tables as $table => $data){
		if($data == true){
			if($table != 'var'){
				$sql = 'SELECT * FROM `'.$table.'`';
			}else{
				$sql = 'SELECT * FROM `'.$table.'` WHERE var_type IS NULL OR var_type != "private"'; //This way we can avoid leaking potentially private information.
			}
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
		if($query != false){
			$t_data = $query->fetch();
		}else{
			$t_data = [];
		}
		$table_struct[$table] = $t_data['Create Table'];
	}
	return [
		'tables' => $tables,
		'struct' => $table_struct,
		'records' => $table_data
	];