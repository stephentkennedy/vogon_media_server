<?php
$old_id = $row['data_meta_id'];
$new_id = $_GET['offset'] + 1;
if($new_id < $old_id){
	$sql1 = 'UPDATE data_meta SET data_meta_id = :new_id WHERE data_meta_id = :old_id';
	$params = [
		':new_id' => $new_id,
		':old_id' => $old_id
	];

	$db->t_query($sql1, $params);
	$message = 'Migrating ID ('.$old_id.' => '.$new_id.').';
}else{
	$message = 'ID does not need to be migrated.';
}
return $message;