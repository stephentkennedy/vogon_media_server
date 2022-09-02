<?php
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
	$struct = [];
	foreach($tables as $table => $bool){
		$sql = 'DESCRIBE `'.$table.'`';
		$query = $db->t_query($sql);
		if($query == false){
			debug_d($db->error);
			continue;
		}
		$struct_data = $query->fetchAll();
		$struct[$table] = $struct_data;
	}
	
	return $struct;