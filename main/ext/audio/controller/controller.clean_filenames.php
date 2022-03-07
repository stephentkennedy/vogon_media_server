<?php
/*
Name: Steph Kennedy
Date: 2/5/2021
Comment: Can't have url unsafe symbols in filenames, so we need to have a script that will clean them up.
*/
$pattern = '/\#\?\&/';
$sql = 'SELECT * FROM data WHERE (data_type = "video" OR data_type = "audio") AND (data_content LIKE "%#%" OR data_content LIKE "%?%" OR data_content LIKE "%&%" OR data_content LIKE "%;%")';
$replace = [
	'#',
	'?',
	'&',
	';'
];

$query = $db->query($sql, []);

if($query != false){
	$matches = $query->fetchAll();
	foreach($matches as $m){
		$filename = $m['data_content'];
		$file_array = explode('/', $filename);
		$root_filename = array_pop($file_array);
		$file_dir = implode('/', $file_array);
		$new_filename = $file_dir.'/'.str_replace($replace, '', $root_filename);
		//debug_d('checking: '.$filename);
		if($new_filename != $filename){
			$check = rename($filename, $new_filename);
			
			if($check == true){
				$sql = 'UPDATE data SET data_content = :content WHERE data_id = :id';
				$params = [
					':content' => $new_filename,
					':id' => $m['data_id']
				];
				$db->query($sql, $params);
				//debug_d('Renamed to: '.$new_filename);
			}
		}
	}
}else{
	debug_d($db->error);
}