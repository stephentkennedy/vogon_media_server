<?php

$new_tables;
$diff_tables;

foreach($new_struct as $table => $fields){
	if(!in_array($table, $cur_struct)){
		$new_tables[] = $table;
	}else{
		foreach($fields as $key => $field){
			$field_name = $field['Field'];
			if($key > 0){
				$prev = $fields[$key - 1]['Field'];
			}else{
				$prev = false;
			}
			$cur_eq = false;
			foreach($cur_struct[$table] as $cf){
				if($cf['Field'] == $field_name){
					$cur_eq = $cf;
					break; //Exit our loop once we've found our field
				}
			}
			
			if($cur_eq == false){
				if(empty($diff_tables[$table])){
					$diff_tables[$table] = [];
				}
				$diff_tables[$table][$field['Field']] = [
					'Type' => $field['Type'],
					'Null' => $field['Null'],
					'Key' => $field['Key'],
					'Default' => $field['Default'],
					'Extra' => $field['Extra'],
					'new' => true,
					'prev' => $prev
				];
			}else{
				$change = [];
				$changes = [];
				if($field['Type'] != $cur_eq['Type']){
					$change[] = 'Type';
					$changes['Type'] = $field['Type'];
				}
				if($field['Null'] != $cur_eq['Null']){
					$change[] = 'Null';
					$changes['Null'] = $field['Null'];
				}
				if($field['Key'] != $cur_eq['Key']){
					$change[] = 'Key';
					$changes['Key'] = $field['Key'];
				}
				if($field['Default'] != $cur_eq['Default']){
					$change[] = 'Default';
					$changes['Default'] = $field['Default'];
				}
				if($field['Extra'] != $cur_eq['Extra']){
					$change[] = 'Extra';
					$changes['Extra'] = $field['Extra'];
				}
				if(empty($diff_tables[$table])){
					$diff_tables[$table] = [];
				}
				$diff_tables[$table][$field['Field']] = [
					'change' => $change,
					'changes' => $changes
					'new' => false,
					'prev' => $prev
				];
			}
			/*
			Name: Stephen Kennedy
			Date: 3/15/21
			Comment: We need to modify this logic to check if a field exists further on in the current structure so I think we're going to switch to loop through the current structure.
			*/
			/*if($cur_struct[$table][$key] != $field){
				if(empty($diff_tables[$table])){
					$diff_tables[$table] = [];
				}
				$diff_tables[$table][$field['Field']] = [
					'Type' => $field['Type'],
					'Null' => $field['Null'],
					'Key' => $field['Key'],
					'Default' => $field['Default'],
					'Extra' => $field['Extra']
				];
			}*/
		}
	}
}

return [
	'new' => $new_tables,
	'change' => $diff_tables
];