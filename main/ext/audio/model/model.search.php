<?php
$sql = '';
$r_sql = 'SELECT * FROM ';
$c_sql = 'SELECT count(DISTINCT `data`.`data_id`) as `count` FROM ';
$params = [];
$clerk = new clerk;

switch($type){
	case 'year':
		$sql .= '`data`, `data_meta` WHERE `data`.`data_type` = "audio" AND `data`.`data_id` = `data_meta`.`data_id` AND `data_meta`.`data_meta_name` = "year" AND `data_meta`.`data_meta_content` LIKE :search';
		$params[':search'] = '%'.$search.'%';
		break;
	case 'genre':
		$sql .= '`data`, `data_meta` WHERE `data`.`data_type` = "audio" AND `data`.`data_id` = `data_meta`.`data_id` AND `data_meta`.`data_meta_name` = "genre" AND `data_meta`.`data_meta_content` LIKE :search';
		$params[':search'] = '%'.$search.'%';
		break;
	case 'artist':
		$sql .= '`data`, `data_meta` WHERE `data`.`data_type` = "audio" AND `data`.`data_id` = `data_meta`.`data_id` AND `data_meta`.`data_meta_name` = "artist" AND `data_meta`.`data_meta_content` LIKE :search';
		$params[':search'] = '%'.$search.'%';
		break;
	case 'album':
		if(!empty($search) && is_numeric($search)){
			$sql .= '`data` WHERE `data_parent` = :search AND data_type = "audio"';
		}else if(!empty($search)){
			$sql .= '`data` WHERE `data_parent` IN(SELECT `data_id` FROM `data` WHERE `data_type` = "album" AND `data_name` LIKE :search)';
			$params[':search'] = '%'.$search.'%';
		}
		break;
	case 'name':
		
		$sql .= '`data` WHERE `data_type` = "audio" AND `data_name` LIKE :search';
	
		break;
	default:
		if($search == false){
			$sql .= '`data` WHERE `data_type` = "audio"';
		}else{
			$sql .='`data`, `data_meta` WHERE `data`.`data_type` = "audio" AND `data`.`data_id` = `data_meta`.`data_id` AND (`data`.`data_name` LIKE :search OR `data`.`data_content` LIKE :search OR `data_meta`.`data_meta_content` LIKE :search) GROUP BY `data`.`data_id`';
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
			$aql = 'SELECT * FROM `data` WHERE `data_id` = :id AND `data_type` = "album"';
			$aarams = [
				':id' => $r['data_parent']
			];
			$a_query = $db->query($aql, $aarams);
			if($a_query != false){
				$album = $a_query->fetch()['data_name'];
				$search_results[$key]['album'] = $album;
			}else{
				$search_results[$key]['album'] = '';
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