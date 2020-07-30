<?php
$clerk = new clerk;
$album = $clerk->getRecord($album);
$sql = 'SELECT * FROM data, data_meta WHERE data.data_type = "audio" AND data.data_parent = :parent AND data.data_id = data_meta.data_id AND data_meta.data_meta_name = "track" ORDER BY data_meta.data_meta_content + 0 ASC';
$params = [
	':parent' => $album['data_id']
];
$query = $db->query($sql, $params);
$members = $query->fetchAll();
foreach($members as $key => $m){
	$meta = $clerk->getMetas($m['data_id']);
	$members[$key]['meta'] = $meta;
}

return[
	'album' => $album,
	'members' => $members
];