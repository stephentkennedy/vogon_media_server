<?php 
$zip = new ZipArchive;
if($zip->open($item['data_content'])){
    $count = $zip->count();
    $array = [];
    for($i=0; $i <= $count; $i++){
        $name = $zip->getNameIndex($i);
        if(!empty($name)){
            $array[] = $name;
        }
    }
    //Because some of these archives use human sortable filenames
    natsort($array);
    $array = array_values($array);
    //If our first entry is a folder, skip it.
    if(substr($array[0], -1) == '/' ){
        $page += 1;
        $count += -1;
    }
    $image_data = $zip->getFromName($array[$page]);
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
                'count' => $count,
                'array' => $array
            ];
        }else{
            $to_return = ['content' => $image_data];
        }
        return $to_return;
    }
    return [
        'error' => true,
        'message' => 'No data at that index',
        'array' => $array
    ];
}else{
    return [
        'error' => true,
        'message' => 'Could not open archive.'
    ];
}