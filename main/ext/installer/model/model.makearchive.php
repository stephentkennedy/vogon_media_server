<?php
	$files_data = load_model('file_prep_linux', [], 'installer');
	return load_model('pack_archive', [
		'files_to_add' => $files_data,
		'filename' => $filename.'.zip',
		'version' => $version
	], 'installer');