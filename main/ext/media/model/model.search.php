<?php
$sql = '';
$r_sql = 'SELECT * FROM ';
$c_sql = 'SELECT count(DISTINCT `data`.`data_id`) as `count` FROM ';
$params = [];
$clerk = new clerk;

/*
Name: Stephen Kennedy
Date: 7/2/2020
Comment: When writing the audio search, I set it up, much like this, so that you could break down and only search certain fields. However, the interface I put into place only supports the general search, so for the moment I'm just writing that and leaving room to add these in later. I think I'd still like the power, but in the interest of keeping moving I'm backburnering them.
*/
switch($type){
	case 'release':
		//break;
	case 'starring':
		//break;
	case 'director':
		//break;
	case 'genre':
		//break;
	case 'series':

		if(!empty($search) && is_numeric($search)){
			$sql .= '`data` WHERE `data_parent` = :search AND data_type = "series"';
		}else if(!empty($search)){
			$sql .= '`data` WHERE `data_parent` IN(SELECT `data_id` FROM `data` WHERE `data_type` = "series" AND `data_name` LIKE :search)';
			$params[':search'] = '%'.$search.'%';
		}
	
		break;
	case 'name':
		//break;
	default:
		if($search == false){
			$sql .= '`data` WHERE (`data_type` = "video" OR `data`.`data_type` = "series")';
		}else{
			$sql .='`data`, `data_meta` WHERE (`data`.`data_type` = "video" OR `data`.`data_type` = "series") AND `data`.`data_id` = `data_meta`.`data_id` AND (`data`.`data_name` LIKE :search OR `data`.`data_content` LIKE :search OR `data_meta`.`data_meta_content` LIKE :search) GROUP BY `data`.`data_id`';
			$params[':search'] = '%'.$search.'%';
		}
		break;
}

$sql .= ' ORDER BY `data`.`data_name`';
$c_query = $db->query($c_sql.$sql , $params);
if($c_query != false){
	$count_raw = $c_query->fetchAll();
	$count = 0;
	foreach($count_raw as $c){
		$count += $c['count'];
	}
	$limit = ' LIMIT '.(($page - 1) * $rpp).', '.$rpp;
	$r_query = $db->query($r_sql.$sql.$limit, $params);
	//debug_d($r_sql.$sql.$limit);
	if($r_query != false){
		$search_results = $r_query->fetchAll();
		
		//Attach all relevant meta data
		foreach($search_results as $key => $r){
			$meta = $clerk->getMetas($r['data_id']);
			$search_results[$key]['meta'] = $meta;
			if($search_results[$key]['data_type'] != 'series'){
				$aql = 'SELECT * FROM `data` WHERE `data_id` = :id AND `data_type` = "series"';
				$aarams = [
					':id' => $r['data_parent']
				];
				$a_query = $db->query($aql, $aarams);
				if($a_query != false){
					$album = $a_query->fetch()['data_name'];
					$search_results[$key]['series'] = $album;
				}else{
					$search_results[$key]['series'] = '';
				}
			}
		}
		
		return [
			'count' => $count,
			'search_results' => $search_results,
			'error' => false
		];
	}else{
		return [
			'count' => 0,
			'search_results' => [],
			'error' => 'Got count, but not search',
			'query_obj' => $r_query,
			'sql' => $r_sql.$sql,
			'param' => $params
		];
	}
}else{
	return [
			'count' => 0,
			'search_results' => [],
			'error' => 'Failed to get count',
			'query_obj' => $c_query,
			'sql' => $c_sql.$sql,
			'param' => $params
		];
}