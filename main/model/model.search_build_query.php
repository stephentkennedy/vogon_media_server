<?php
/*
Name: Stephen Kennedy
Date: 12/30/2019
Comment: If all goes according to plan, we should be passing this model a pre-parsed search query in the form of an array.
That array should look like this.
$array = [ //Top level, split into chunks of OR statements
	0 => [ //Second level, split into chunks of AND statements, in both cases there should be at least one member of each level. 
		0 => [
			'members' => [] //An array of things to search for
			'not' => [], //An array of things to search for and exclude
			'keys' => [], //An array of items to search for in specific columns
		]
	]
]
*/
$statement = '';
$structure_data = load_model('get_table_struct', ['tables' => $tables]);
$columns = $structure_data['columns'];

$params = [];
//IDs to increment so we can build our params array
$member_id = 0;
$key_id = 0;
$not_id = 0;

$or_statment = [];
foreach($query as $or_level){
	$and_statement = [];
	foreach($or_level as $and_level){
		foreach($and_level['members'] as $member){
			$loop_member_statement = [];
			foreach($columns as $column){
				$loop_member_statement[] = '(`'.$column.'` LIKE :member'.$member_id.')';
				if(!isset($params[':member'.$member_id])){
					$params[':member'.$member_id] = '%'.$member.'%';
				}
			}
			$loop_member_statement = '('.implode(' OR ', $loop_member_statement).')';
			$and_statement[] = $loop_member_statement;
			$member_id++;
		}
		foreach($and_level['keys'] as $keys){
			$temp = explode(':', $keys);
			$k_column = trim($temp[0]);
			$k_value = trim($temp[1]);
			$k_value = preg_replace('/[^a-zA-Z0-9\s]/', '', $k_value);
			$loop_key_statement = [];
			foreach($columns as $column){
				if(stristr($column, $k_column) === false 
					|| empty($k_value) 
					|| empty($k_column)){
					//If we aren't looking in this column, or during the parsing the value or column became blank skip to the next column
					continue;
				}
				$loop_key_statement[] = '(`'.$column.'` LIKE :key'.$key_id.')';
				$params[':key'.$key_id] = '%'.$k_value.'%';
			}
			$loop_key_statement = '('.implode(' OR ', $loop_key_statement).')';
			$and_statement[] = $loop_key_statement;
			$key_id++;
		}
		foreach($and_level['not'] as $not){
			$not = trim($not);
			$not = ltrim($not, '-');
			$loop_not_statement = [];
			foreach($columns as $column){
				$type = $structure_data['types'][$column];
				if(in_array($type, ['int', 'float', 'tinyint']) && !is_numeric($not)){
					continue;
				}
				$loop_not_statement[] = '(`'.$column.'` NOT LIKE :not'.$not_id.')';
				$params[':not'.$not_id] = '%'.$not.'%';
			}
			$loop_not_statement = '('.implode(' AND ', $loop_not_statement).')';
			$and_statement[] = $loop_not_statement;
			$not_id++;
		}
	}
	$and_statement = '('.implode(' AND ', $and_statement).')';
	$or_statement[] = $and_statement;
}
$statement = implode(' OR ', $or_statement);
$table_statement = '';
foreach($tables as $table){
	$table_statement .= '`'.$table.'`,';
}
if($statement == '()'){
	$statement = '1';
}
if(!empty($links)){
	$links = '('.implode(' AND ', $links).')';
	if($statement != '1'){
		$statement .= ' AND '.$links;
	}else{
		$statement = $links;
	}
}
$table_statement = rtrim($table_statement,',');
$count_statement = 'SELECT count(*) as `count` FROM '.$table_statement.' WHERE '.$statement;
$query = $db->query($count_statement, $params);
if($query !== false){
	$count = $query->fetch()['count'];
	$statement = 'SELECT * FROM '.$table_statement.' WHERE '.$statement;
	
	return [
		'sql' => $statement,
		'total' => $count,
		'params' => $params
	];
}else{
	return false;
}