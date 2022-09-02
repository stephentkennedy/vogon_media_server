<?php

$clerk = new clerk;
$record = $clerk->getRecord($row['data_id']);
if(empty($record)){
	return '[Unknown ID]';
}else{
	if($record['data_type'] == 'audio' || $record['data_type'] == 'video'){
		$content = $record['data_content'];
		if(file_exists($content)){
			return '';
		}else{
			$clerk->updateMetas($row['data_id'], [
				'orphan' => 1
			]);
			return 'File missing for entry: '.$row['data_id'].'). '.$row['data_name'];
		}
	}
}