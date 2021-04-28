<?php
$sql = 'SELECT * FROM `data`';
$cql = 'SELECT count(*) as `count` FROM `data`';
$params = [];
$search_cols = [
	'data_id',
	'data_name',
	'data_content',
	'data_type',
	'data_parent',
	'data_status',
	'create_date',
	'last_edit',
	'user_key'
];
if(!empty($search) && $search_col == 'all'){
	$append = ' WHERE data_id LIKE :search OR data_name LIKE :search OR data_slug LIKE :search OR data_content LIKE :search OR data_type LIKE :search OR data_parent LIKE :search or data_status LIKE :search OR create_date LIKE :search OR last_edit LIKE :search OR user_key LIKE :search';
	$sql .= $append;
	$cql .= $append;
	$params[':search'] = '%'.$search.'%';
}else if(!empty($search) && in_array($search_col, $search_cols)){
	$append = ' WHERE `'.$search_col.'` LIKE :search';
	$sql .= $append;
	$cql .= $append;
	$params[':search'] = '%'.$search.'%';
}

if(!empty($order_by) && in_array($order_by, $search_cols)){
	$sql .= ' ORDER BY `'.$order_by.'`';
	if(!empty($dir) && ($dir == 'ASC' || $dir == 'DESC')){
		$sql .= ' '.$dir;
	}
}

$c_query = $db->query($cql, $params);

if($c_query === false){
	debug_d($cql);
	debug_d($db->error);
	return;
}

$count = $c_query->fetch()['count'];

$pages = ceil($count / $limit);

$page = floor($offset / $limit) + 1;

$sql .= ' LIMIT '.$offset.','.$limit;

$query = $db->query($sql, $params);

if($query === false){
	debug_d($sql);
	debug_d($db->error);
	return;
}

$rows = $query->fetchAll();
return [
	'rows' => $rows,
	'count' => $count,
	'pages' => $pages,
	'page' => $page
];
