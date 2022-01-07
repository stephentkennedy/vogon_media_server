<?php
global $user;
$clerk = new clerk;

$sql = 'SELECT * FROM history WHERE data_id IN (SELECT data_id FROM data WHERE data_parent = :parent AND data_type != "season") AND user_key = :user ORDER BY last_edit DESC LIMIT 1';
$params = [
	':parent' => $id,
	':user' => $user['user_key']
];
$query = $db->query($sql, $params);

if($query == false){
	die($db->error);
}

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
	$pad = 2.5 * $min;
	if($current >= ($item['meta']['length'] - $pad)){
		//We're within our threshold to skip to the next episode
		$have = false;
		
		$sql = 'SELECT * FROM data, data_meta WHERE data_meta.data_meta_name = "episode_ord" AND data_meta.data_meta_content = :ord AND data_meta.data_id = data.data_id AND data.data_parent = :series';
		$params = [
			':ord' => ((int)$item['meta']['episode_ord'] + 1),
			':series' => $id
		];
		$query = $db->query($sql, $params);
		if($query == false){
			die($db->error);
		}
		$episodes = $query->fetchAll();
		if(!empty($episodes)){
			foreach($episodes as $e){
				$meta = $clerk->getMetas($e['data_id']);
				if($meta['season'] == $item['meta']['season']){
					$have = true;
					$return = [
						'result' => true,
						'id' => $e['data_id'],
						'text' => 'Start Next Episode'
					];
					break; //Break out of our loop.
				}
			}
		}
		
		if($have == false){
			
			$season = $clerk->getRecord($item['meta']['season'], true);
			
			$sql = 'SELECT * FROM data, data_meta WHERE data_meta.data_meta_name = "season_ord" AND data_meta.data_id = data.data_id AND data.data_type = "season" AND data.data_parent = :series AND data_meta.data_meta_content = :ord ORDER BY data_meta.data_meta_content + 0 ASC';
			$params = [
				':series' => $id,
				':ord' => $season['meta']['season_ord'] + 1
			];
			$query = $db->query($sql, $params);
			if($query == false){
				die($db->error);
			}
			$next_season = $query->fetch();
			if(empty($next_season)){
				return [
					'result' => false,
					'reason' => 'last_season'
				];
			}else{
				$sql = 'SELECT * FROM data, data_meta WHERE data.data_id = data_meta.data_id AND data_meta.data_meta_name = "season" AND data_meta.data_meta_content = :season';
				$params = [
					':season' => $next_season['data_id']
				];
				$query = $db->query($sql, $params);
				if($query == false){
					die($db->error);
				}
				$episodes = $query->fetchAll();
				foreach($episodes as $e){
					$meta = $clerk->getMetas($e['data_id']);
					if($meta['episode_ord'] == 0){
						$return = [
							'result' => true,
							'id' => $e['data_id'],
							'text' => 'Start Next Season'
						];
						break;
					}
				}
			}
		}
		
	}else{
		//Then we need to resume the current episode
		$return = [
			'result' => true,
			'id' => $item['data_id'],
			'text' => 'Resume Playing'
		];
	}
}

return $return;