<?php

$thumb_dir = ROOT . DIRECTORY_SEPARATOR . 'upload' . DIRECTORY_SEPARATOR . 'thumbs' . DIRECTORY_SEPARATOR;
$sql = 'SELECT * FROM `data` WHERE `data_type` = "video" ORDER BY `data_name` ASC';
$videos = $db->t_query($sql, [])->fetchAll();
$clerk = new clerk;
foreach($videos as $key => $v){
	$metas = $clerk->getMetas($v['data_id']);
	
	$temp = $metas['poster'];
	$temp = explode('/', $temp);
	$temp = array_pop($temp);

	if(file_exists($thumb_dir.$temp)){
		$thumb = '/upload/thumbs/'.$temp;
	}else{
		$thumb = $metas['poster'];
	}
	$metas['thumb'] = $thumb;
	
	$videos[$key]['metas'] = $metas;
	
}
return [
	'videos' => $videos
];