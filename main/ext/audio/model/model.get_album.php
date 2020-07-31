<?php
$clerk = new clerk;
global $user;
$album = $clerk->getRecord($album, true);
$sql = 'SELECT * FROM data, data_meta WHERE data.data_type = "audio" AND data.data_parent = :parent AND data.data_id = data_meta.data_id AND data_meta.data_meta_name = "track" ORDER BY data_meta.data_meta_content + 0 ASC';
$params = [
	':parent' => $album['data_id']
];
$query = $db->query($sql, $params);
$members = $query->fetchAll();
foreach($members as $key => $m){
	$meta = $clerk->getMetas($m['data_id']);
	$members[$key]['meta'] = $meta;
	if(!empty($meta['history']) && $meta['history'] == true){
		$sql = 'SELECT * FROM history WHERE data_id = :id AND user_key = :user';
		$params = [
			':id' => $m['data_id'],
			':user' => $user['user_key']
		];
		$query = $db->query($sql, $params);
		$history = $query->fetch();
		if(!empty($history)){
			$members[$key]['listened'] = $history['history_val'];
		}else{
			$members[$key]['listened'] = 0;
		}
	}
}

return[
	'album' => $album,
	'members' => $members
];