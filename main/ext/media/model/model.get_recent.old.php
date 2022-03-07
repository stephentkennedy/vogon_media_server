<?php
global $user;
$clerk = new clerk;

if(!empty($user) && !empty($user['user_key'])){
	
	if(empty($limit)){
		$limit = 10;
	}
	
	$sql = 'SELECT data.*,
	h1.last_edit AS `watched`,
	m1.data_meta_content AS `poster`
	FROM data
	LEFT JOIN(SELECT * FROM history WHERE history.user_key = :user) h1 on `data`.`data_id` = h1.data_id
	LEFT JOIN(SELECT * FROM data_meta WHERE data_meta_name = "poster") m1 on `data`.`data_id` = m1.data_id
	WHERE h1.last_edit > :thirty_days 
	AND h1.last_edit IS NOT NULL
	ORDER BY `watched` DESC';
	$params = [
		':user' => $user['user_key'],
		':thirty_days' => db_date('-30 days')
	];
	$query = $db->query($sql, $params);
	if($query == false){
		return [
			'success' => false,
			'error' => $db->error->getMessage()
		];
	}else{
		$results = $query->fetchAll();
		
		/*
		Name: Steph Kennedy
		Date: 8/12/2020
		Comment: MySQL doesn't really order grouped queries effectively so we're going to order our results
		*/
		
		$ordered = [];
		foreach($results as $r){
			if(empty($ordered[$r['data_parent']]) && $r['data_type'] != 'video'){
				$r['parent'] = $clerk->getRecord($r['data_parent']);
				$ordered[$r['data_parent']] = $r;
				
			}else if ($r['data_type'] == 'video'){
				$ordered[] = $r;
			}
		}
		
		$results = array_slice(array_values($ordered), 0, $limit);
		
		if(!empty($results)){
			return [
				'recent' => $results,
				'success' => true
			];
		}else{
			return [
				'success' => false,
				'error' => 'No history within last 30 days'
			];
		}
	}
}else{
	return [
		'success' => false,
		'error' => 'No user data'
	];
}