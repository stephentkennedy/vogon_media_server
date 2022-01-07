<?php
$id = $_GET['id'];
$to_return = [
	'saved' => false
];
if(is_numeric($id)){
	load_model('toggle_favorite', ['id' => $id], 'audio');
	$to_return['saved'] = true;
}
echo load_view('json', $to_return);
