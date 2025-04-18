<?php
// Injected: $data_id (int), $data_content (string)

load_class('db_handler');
$d  = new db_handler('data');
$dm = new db_handler('data_meta');
$dh = $d->meta_link($dm, [
    'meta_key_field'   => 'data_meta_name',
    'meta_value_field' => 'data_meta_content'
]);

$video_path = $data_content;
if (!file_exists($video_path)) {
    $video_path = url2dir($video_path);
}

if (empty($video_path) || !file_exists($video_path)) {
    return false;
}

//$candidate = preg_replace('/\.[^.]+$/', '.srt', $video_path);

if(substr($video_path, -4, 1) == '.'){
    $candidate = substr($video_path, 0, -4).'.srt';
}else{
    return false;
}

if (!file_exists($candidate)) {
    return false;
}

/*$dh->child->addRecord([
    'data_id'           => $data_id,
    'name'    => 'srt_subtitles',
    'content' => $candidate,
]);*/
$clerk = new clerk;



$clerk->updateMetas($data_id, [
    'srt_subtitles' => $candidate
]);

return $candidate;
