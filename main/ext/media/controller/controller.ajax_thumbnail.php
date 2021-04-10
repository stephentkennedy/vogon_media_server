<?php
$model_data = load_model('update_thumbnail', [
	'id' => (int)$_GET['id'],
	'seconds' => $_GET['seconds']
], 'media');

echo load_view('json', $model_data);