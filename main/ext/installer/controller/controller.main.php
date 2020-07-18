<?php
	$table_data = load_model('gettables', [], 'installer');
	$filename = load_model('makedbfiles', $table_data, 'installer');
	$archive_data = [
		'filename' => 'vogon_build_'.date('m_d_y_h').'.zip'
	];
	load_model('makearchive', $archive_data, 'installer');
	echo $filename;
	echo '<br><a href="'.URI.'">Back</a>';
?>