<?php
	$write = [];
	foreach($tables as $table => $data){
		$write[$table] = [
			'name' => $table,
			'create' => $struct[$table],
			'records' => $records[$table]
		];
	}
	$write = json_encode($write);
	$file_data = [
		'filename' => 'tables.json',
		'ext' => 'installer',
		'content' => $write
	];
	$write_data = load_model('write_file', $file_data);
	if(gettype($write_data) == 'bool'){
		return false;
	}else if($write_data['success'] == false){
		return false;
	}else{
		return $write_data['filename'];
	}