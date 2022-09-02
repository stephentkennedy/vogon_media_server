<?php
if(isset($_GET['dir'])){
	$dir = urldecode($_GET['dir']);
}else{
	$dir = ROOT;
}
$dir_data = load_model('dir_scan', ['dir' => $dir], 'filebrowser');
$html_content = load_view('dir_ajax', $dir_data, 'filebrowser');
$return = [
	'content' => $html_content,
	'dir' => preg_replace('~\/{2,}~', '/', $dir)
];
echo load_view('json', $return);