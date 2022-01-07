<?php

$return = [];
$clerk = new clerk;

foreach($tracks as $t){
	$album_id = $t['data_parent'];
	if(empty($return[$album_id])){
		$album = $clerk->getRecord($album_id);
		$return[$album_id] = $album['data_name'];
	}
}
return $return;