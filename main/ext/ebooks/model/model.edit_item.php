<?php 
$clerk = new clerk;
$meta_data = [
    'author' => $author,
    'year' => $year
];
$clerk->updateMetas($id, $meta_data);
$sql = 'SELECT * FROM data WHERE data_type = "series" AND data_name = :search ORDER BY data_id ASC LIMIT 1';
$params = [
	':search' => $series
];
$query = $db->query($sql, $params);
$check = $query->fetch();
if(empty($check)){
	$album_id = $clerk->addRecord([
		'name' => $series,
		'type' => 'series'
	]);
}else{
	$album_id = $check['data_id'];
}
$clerk->updateRecord(['name' => $data_name, 'parent' => $album_id], $id);