<?php

$mime = mime_content_type($f);

$name = explode(DIRECTORY_SEPARATOR, $f);
$name = array_pop($name);
$message = 'Checking "'.$name.'"<br>';
$message .= 'Mime: '.$mime.'<br>';
$mime = explode('/', $mime);
load_class('db_handler');
$hand = new db_handler('data');

switch(strtolower(substr($f, -4))){
	case '.pdf':
		$search = [
			'content' => $f,
			'type' => 'pdf'
		];
		$check = $hand->getRecord($search);
		if(empty($check)){
			$id = $hand->addRecord($search);
			$message .= 'Adding to database.';
			$message .= '<br>Generating Thumbnail.';
			load_model('create_pdf_thumbnail', [
				'id' => $id
			], 'ebooks');
		}else{
			$message .= 'File already exists in database.<br>Skipping.';
		}
	
		break;
	case '.cbz':
		$search = [
			'content' => $f,
			'type' => 'cbz'
		];
		$check = $hand->getRecord($search);
		if(empty($check)){
			$id = $hand->addRecord($search);
			$message .= 'Adding to database.';
			$message .= '<br>Generating Thumbnail.';
			load_model('create_cbz_thumbnail', [
				'id' => $id
			], 'ebooks');
		}else{
			$message .= 'File already exists in database.<br>Skipping.';
		}
		break;
	case 'epub':
		if(strtolower(substr($f, -5)) != '.epub'){
			$message .= 'File is not a known ebook format.<br>Skipping.';
			break;
		}
		$search = [
			'content' => $f,
			'type' => 'epub'
		];
		$check = $hand->getRecord($search);
		if(empty($check)){
			$hand->addRecord($search);
			$message .= 'Adding to database.';
		}else{
			$message .= 'File already exists in database.<br>Skipping.';
		}
		break;
	default:
		$message .= 'File is not a known ebook format.<br>Skipping.';
		break;
}

return $message;
