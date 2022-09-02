<?php
$clerk = new clerk;
if(!empty($_GET['name'])){
	$name = $_GET['name'];
}
$cur = $_GET['id'];

$record = $clerk->getRecord($cur, true);
if(!empty($record['meta']['season'])){
	$season = $record['meta']['season'];
	$sql = 'SELECT * FROM data, data_meta WHERE data.data_id = data_meta.data_id AND data.data_id IN(SELECT data_id FROM data_meta WHERE data_meta_name = "season" AND data_meta_content = :season) AND data_meta.data_meta_name = "episode_ord" ORDER BY data_meta.data_meta_content + 0 ASC';
	$params = [
		':season' => $season
	];
	$query = $db->t_query($sql, $params);
	if($query != false){
		$results = $query->fetchAll();
		$list = [];
		$cur_id = 0;
		foreach($results as $k => $v){
			if($v['data_id'] == $cur){
				$cur_id = $k;
			}
			$meta = $clerk->getMetas($v['data_id']);
			if(empty($meta['animorphic'])){
				$meta['animorphic'] = 0;
			}
			$list[] = [
				'loc' => str_replace(ROOT, '', $v['data_content']),
				'id' => $v['data_id'],
				'name' => $v['data_name'],
				'poster' => str_replace(ROOT, '', $meta['poster']),
				'animorphic' => $meta['animorphic']
			];
		}
	}
	$output = [
		'list' => $list,
		'current' => $cur_id
	];
	$season = $clerk->getRecord($season, true);
	$sql = 'SELECT * FROM data, data_meta WHERE data.data_id = data_meta.data_id AND data.data_type ="season" AND data.data_parent = :parent AND data_meta.data_meta_name = "season_ord" AND data_meta.data_meta_content + 0 > :ord ORDER BY data_meta.data_meta_content + 0 ASC';
	$params = [
		':parent' => $season['data_parent'],
		':ord' => (int)$season['meta']['season_ord'] //I actually wonder if casting on the PHP side of this transaction does anything
	];
	$query = $db->t_query($sql, $params);
	if($query != false){
		$next_season = $query->fetch();
		if(!empty($next_season)){
			$sql = 'SELECT * FROM data, data_meta WHERE data.data_id = data_meta.data_id AND data.data_type = "tv" AND data.data_id IN(SELECT data_id FROM data_meta WHERE data_meta_name = "season" AND data_meta_content = :season) AND data_meta.data_meta_name = "episode_ord" AND data_meta.data_meta_content = "0"';
			$params = [
				':season' => $next_season['data_id']
			];
			$query = $db->t_query($sql, $params);
			if($query != false){
				$next_episode = $query->fetch();
				$meta = $clerk->getMetas($next_episode['data_id']);
				$output['next_season'] = [
					'season_name' => $next_season['data_name'],
					'episode_name' => $next_episode['data_name'],
					'first_episode' => $next_episode['data_content'],
					'episode_id' => $next_episode['data_id'],
					'poster' => str_replace(ROOT, '', $meta['poster'])
				];
			}
		}
	}
}else{
	$sql = 'SELECT * FROM data WHERE data_type = "series" AND data_name = :name';
	$params = [':name' => $name];
	$query = $db->t_query($sql, $params);
	if($query != false){
		$id = $query->fetch()['data_id'];
		$sql = 'SELECT * FROM data WHERE data_parent = :id AND data_type = "tv" ORDER BY data_name ASC'; //Put in order logic later
		$params = [':id' => $id];
		$query = $db->t_query($sql, $params);
		if($query != false){
			$results = $query->fetchAll();
			$list = [];
			$cur_id = 0;
			foreach($results as $k => $v){
				if($v['data_id'] == $cur){
					$cur_id = $k;
				}
				$meta = $clerk->getMetas($v['data_id'], ['name' => 'poster']);
				$list[] = [
					'loc' => str_replace(ROOT, '', $v['data_content']),
					'id' => $v['data_id'],
					'name' => $v['data_name'],
					'poster' => str_replace(ROOT, '', $meta['poster'])
				];
			}
		}
	}
	$output = [
		'list' => $list,
		'current' => $cur_id
	];
}


header('Content-Type: application/json;charset=utf-8');
echo json_encode($output);
//Old Logic
/*$sql = 'SELECT * FROM data WHERE data_type = "series" AND data_name = :name';
$params = [':name' => $name];
$query = $db->t_query($sql, $params);
if($query != false){
	$id = $query->fetch()['data_id'];
	$sql = 'SELECT * FROM data WHERE data_parent = :id ORDER BY data_name ASC'; //Put in order logic later
	$params = [':id' => $id];
	$query = $db->t_query($sql, $params);
	if($query != false){
		$results = $query->fetchAll();
		$list = [];
		$cur_id = 0;
		foreach($results as $k => $v){
			if($v['data_id'] == $cur){
				$cur_id = $k;
			}
			$list[] = str_replace(ROOT, '', $v['data_content']);
		}
		$output = [
			'list' => $list,
			'current' => $cur_id
		];
		header('Content-Type: application/json;charset=utf-8');
		echo json_encode($output);
	}
}*/