<?php
if(empty($change)){
	return;
}
foreach($change as $table => $fields){
	foreach($fields as $field => $f){
		if($f['new'] == true){
			$sql = 'ALTER TABLE `'.$table.'` ADD `'.$field.'`'. $f['Type'];
			if($f['Null'] == 'NO'){
				$sql .= ' NOT NULL';
			}else{
				$sql .= ' NULL';
			}
			if(!empty($f['Default'])){
				
				$sql .= ' DEFAULT '.$f['Default'];
			}
			if(!empty($f['Extra'])){
				$sql .= ' '.$f['Extra'];
			}
			if($f['prev'] == false){
				$sql .= ' FIRST';
			}else{
				$sql .= ' AFTER `'.$f['prev'].'`';
			}
		}else{
			if(empty($f['Type'])){
				$f['Type'] = '';
			}
			$sql = 'ALTER TABLE `'.$table.'` MODIFY `'.$field.'`'. $f['Type'];
			if(empty($f['Null']) || $f['Null'] == 'NO'){
				$sql .= ' NOT NULL';
			}else{
				$sql .= ' NULL';
			}
			if(!empty($f['Default'])){
				
				$sql .= ' DEFAULT '.$f['Default'];
			}
			if(!empty($f['Extra'])){
				$sql .= ' '.$f['Extra'];
			}
		}
		$db->t_query($sql);
	}
}