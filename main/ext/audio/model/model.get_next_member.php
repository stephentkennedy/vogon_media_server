<?php
global $user;
$clerk = new clerk;

$sql = 'SELECT * FROM history WHERE data_id IN (SELECT data_id FROM data WHERE data_parent = :parent AND data_type = "audio") AND user_key = :user ORDER BY last_edit DESC LIMIT 1';
$params = [
	':parent' => $id,
	':user' => $user['user_key']
];
$query = $db->query($sql, $params);

$result = $query->fetch();

if($result == false){
	//We don't have a history entry for this user.
	$return = [
		'result' => false,
		'reason' => 'no_history'
	];
}else{
	$item = $clerk->getRecord($result['data_id'], true); //Get Item & meta data
	$current = $result['history_val'];
	$min = 60;
	$pad = .5 * $min;
	if($current >= ($item['meta']['length'] - $pad)){
		//We're within our threshold to skip to the next episode
		$have = false;
		$sql = 'SELECT * FROM data, data_meta WHERE data_meta.data_meta_name = "track" AND data_meta.data_meta_content = :ord AND data_meta.data_id = data.data_id AND data.data_parent = :series';
		$params = [
			':ord' => ((int)$item['meta']['track'] + 1),
			':series' => $id
		];
		$query = $db->query($sql, $params);
		if($query == false){
			die($db->error);
		}
		$next = $query->fetch();
		if(empty($next)){
			$return = [
				'result' => false,
				'reason' => 'finished_album'
			];
		}else{
			if(empty($next['data_name'])){
				$next['data_name'] = 'next';
			}
			$return = [
				'result' => true,
				'id' => $next['data_id'],
				'text' => 'Play '.$next['data_name']
			];
		}
	}else{
		if(empty($item['data_name'])){
				$item['data_name'] = 'track';
			}
		$return = [
			'result' => true,
			'id' => $item['data_id'],
			'text' => 'Resume '.$item['data_name']
		];
	}
}

return $return;