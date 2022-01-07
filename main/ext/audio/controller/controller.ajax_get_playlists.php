<?php
$clerk = new clerk;
$playlists = $clerk->getRecords([
	'type' => 'playlist',
	/*'orderby' => '`data_name` ASC'*/
]);
$output = [];
foreach($playlists as $p){
	$output[] = [
		'title' => $p['data_name'],
		'id' => $p['data_id'],
		'list' => $p['data_content']
	];
}
echo load_view('json', $output);