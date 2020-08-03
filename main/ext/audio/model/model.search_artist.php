<?php
$sql = '';
$r_sql = 'SELECT * FROM ';
$c_sql = 'SELECT count(DISTINCT `data_meta`.`data_meta_content`) as `count` FROM ';
$params = [];

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
			$sql .= '`data_meta` WHERE `data_meta_name` = "artist" GROUP BY `data_meta_content`';
		}else{
			$sql .='`data_meta` WHERE `data_meta_name` = "artist" AND `data_meta_content` LIKE :search GROUP BY `data_meta_content`';
			$params[':search'] = '%'.$search.'%';
		}	
		break;
}
$sql .= ' ORDER BY `data_meta`.`data_meta_content`';
$c_query = $db->query($c_sql.$sql , $params);
if($c_query != false){
	$count_raw = $c_query->fetchAll();
	$count = 0;
	foreach($count_raw as $c){
		$count += $c['count'];
	}
	$limit = ' LIMIT '.(($page - 1) * $rpp).', '.$rpp;
	$r_query = $db->query($r_sql.$sql.$limit, $params);
	if($r_query != false){
		$search_results = $r_query->fetchAll();
		
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