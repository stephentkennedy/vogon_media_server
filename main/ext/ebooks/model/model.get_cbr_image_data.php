<?php 
$zip = new RarArchive;
if($zip->open($item['data_content'])){
    $entries = $zip->getEntries();
    ob_start();
    debug_d($entries);
    return ob_get_clean();
    /*$image_data = $zip->getFromIndex($page);*/
    if($image_data != false){
        $file_info = new finfo(FILEINFO_MIME);
        $mime = $file_info->buffer($image_data);
        $mime = explode(';', $mime)[0];
        $mime_array = explode('/', $mime);
        if($mime_array[0] == 'image'){
            $image_data = base64_encode($image_data);
            $to_return = [
                'image_data' => 'data:'.$mime.';base64,'.$image_data,
                'mime' => $mime,
                'count' => $zip->count()
            ];
        }else{
            $to_return = ['content' => $image_data];
        }
        return $to_return;
    }
    return [
        'error' => true,
        'message' => 'No data at that index'
    ];
}else{
    return [
        'error' => true,
        'message' => 'Could not open archive.'
    ];
}