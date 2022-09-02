<?php
$clerk = new clerk;
$record = $clerk->getRecord($_GET['id'], true);
if($record['data_type'] == 'video' || $record['data_type'] == 'tv'){
	echo 'Are you sure you want to delete '.$record['data_name'].'.<br><br>
	<form method="post">
		<input type="hidden" name="action" value="delete">
		<input type="hidden" name="id" value="'.$record['data_id'].'">
		<input id="remove_file" type="checkbox" name="remove_file" value="1"> <label class="inline" for="remove_file">Remove File?</label><br>
		<button type="submit">Delete Video</button>
	</form>';
}else{
	echo 'Provided ID is for non-video record.';
}