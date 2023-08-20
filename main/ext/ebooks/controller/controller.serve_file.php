<?php
set_time_limit(0);
//header('Content-Description: File Transfer');
//header('Content-Type: application/octet-stream');
//header('Content-Disposition: attachment; filename="'.basename($file).'"');
//header('Expires: 0');
//header('Cache-Control: must-revalidate');
//header('Pragma: public');
//header('Content-Length: ' . filesize($file));
//readfile($file);

if(!function_exists('serve_file')){
    function serve_file($filepath, $new_filename=null) {
        $filename = basename($filepath);
        if (!$new_filename) {
            $new_filename = $filename;
        }
        $mime_type = mime_content_type($filepath);
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        //header('Content-type: '.$mime_type);
        header('Content-Disposition: attachment; filename="'.$new_filename.'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filepath));
        readfile($filepath);
    }
}

$id = get_slug_part(2);

if(!is_numeric($id)){
    http_response_code(404);
    die();
}

$item = load_model('get_item_by_id', ['id' => $id], 'ebooks');

if(empty($item)){
    http_response_code(404);
    die(); 
}

$filename = $item['data_content'];

if(!file_exists($filename)){
    http_response_code(404);
    die();
}

serve_file($filename);

?>