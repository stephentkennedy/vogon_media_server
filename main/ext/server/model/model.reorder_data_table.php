<?php
$old_id = $row['data_id'];
$new_id = $_GET['offset'] + 1;
/*
Name: Stephen Kennedy
Date: 3/5/21
Comment: Tables we need to update:
-data
-data_meta
-history
In addition we'll need to flush the cache table and reset the auto increment for everything.
*/
if($new_id < $old_id){
	$sql1 = 'UPDATE data SET data_id = :new_id WHERE data_id = :old_id';
	$sql2 = 'UPDATE data SET data_parent = :new_id WHERE data_parent = :old_id';
	$sql3 = 'UPDATE data_meta SET data_id = :new_id WHERE data_id = :old_id';
	$sql4 = 'UPDATE history SET data_id = :new_id WHERE data_id = :old_id';
	$params = [
		':new_id' => $new_id,
		':old_id' => $old_id
	];

	$db->query($sql1, $params);
	$db->query($sql2, $params);
	$db->query($sql3, $params);
	$db->query($sql4, $params);
	$message = 'Migrating ID ('.$old_id.' => '.$new_id.').';
}else{
	$message = 'ID does not need to be migrated.';
}
return $message;