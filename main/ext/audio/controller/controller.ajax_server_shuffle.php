<?php
$params = [];
if(empty($_POST['search'])){
	$sql = 'SELECT data.data_id, m1.data_meta_content AS `history` FROM data LEFT JOIN (SELECT * FROM data_meta WHERE data_meta_name = "history") m1 on `data`.`data_id` = m1.data_id WHERE data.data_type = "audio" AND m1.data_meta_content IS NULL';
}else{
	
	/* Possible Search Matches
		'history',
		'year',
		'genre',
		'artist',
		'album',
		'name'
	*/
	
	$sql = 'SELECT data.data_id, 
	m1.data_meta_content AS `history`,
	data_meta.data_meta_content
	FROM data 
	LEFT JOIN (SELECT * FROM data_meta WHERE data_meta_name = "history") m1 on `data`.`data_id` = m1.data_id,
	data_meta
	WHERE data.data_type = "audio" AND m1.data_meta_content IS NULL AND
	data_meta.data_id = data.data_id AND
	(data.data_name LIKE :search
	OR data.data_content LIKE :search
	OR data_meta.data_meta_content like :search)';
	
	$params[':search'] = '%'.$_POST['search'].'%';
}

if(isset($_POST['prev']) && is_array($_POST['prev']) && isset($_POST['prev'][0]) && is_int($_POST['prev'][0])){
	$sql .= ' AND data.data_id NOT IN('.implode(', ',$_POST['prev']).')';
}

$sql .= ' ORDER BY RAND() LIMIT 100';

$query = $db->t_query($sql, $params);

if($query !== false){
	$results = $query->fetchAll();
	$final = [];
	foreach($results as $r){
		$final[] = $r['data_id'];
	}
	$return = [
		'chunk' => $final,
		'error' => false
	];
}else{
	$return = [
		'chunk' => [],
		'error' => true,
		'message' => $db->error->getMessage()
	];
}

echo load_view('json', $return);