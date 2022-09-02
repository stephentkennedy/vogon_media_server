<?php
$clerk = new clerk;
$record = $clerk->getRecord($id, true);
if($record['data_type'] != 'video' && $recrod['data_type'] != 'tv'){
	//Break out because we're not dealing with the right record type to be using this model.
	return;
}
if($delete_file == true){
	$file_loc = $record['data_content'];
	$file_loc = trueLoc($file_loc);
	if($file_loc !== false){
		unlink($file_loc);
	}
}
$clerk->removeRecord($id);