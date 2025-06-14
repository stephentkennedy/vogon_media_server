<?php 
$zip = new ZipArchive;
if($zip->open($item['data_content']) === true){
    $count = $zip->count();
    if($count == 0){
        return [
            'error' => true,
            'message' => 'This archive appears to be empty.',
            'count' => $count
        ];
    }
    $array = [];
    for($i=0; $i <= $count; $i++){
        $name = $zip->getNameIndex($i);
        //debug_d($name);
        if(
            !empty($name)
            || $name === 0
        ){
            $array[] = $name;
        }
    }
    //Because some of these archives use human sortable filenames
    natsort($array);
    $array = array_values($array);
    if(empty($array)){
        return [
            'error' => true,
            'message' => 'No data was returned when traversing the archive for page names.',
            'array' => $array,
            'count' => $count
        ];
    }
    //If our first entry is a folder, skip it.
    if(@substr($array[0], -1) == '/'){
        $page += 1;
        $count += -1;
    }
    if(empty($array[$page])){
        return [
            'error' => true,
            'message' => 'No data at that index',
            'array' => $array
        ];
    }
    $image_data = $zip->getFromName($array[$page]);
    if($image_data != false){
        $file_info = new finfo(FILEINFO_MIME);
        $mime = $file_info->buffer($image_data);
        $mime = explode(';', $mime)[0];
        $mime_array = explode('/', $mime);

        if(
            $_SESSION['resize_cbz'] == true
            && !empty($_GET['windowWidth'])
            && $mime_array[0] == 'image'
        ){
            //$file_handle = fopen('php://memory', 'w+');
            //fwrite($file_handle, $image_data);
            //fseek($file_handle, 0); 
            //fclose($file_handle);
            $im = new \Imagick();

            $area_limit = \Imagick::getResourceLimit(\Imagick::RESOURCETYPE_AREA);

            list($width, $height, $type, $attr) = getimagesizefromstring($image_data);

            $area = $width * $height;

            
            //$width = $im->getImageWidth();
            //$height = $im->getImageHeight();
            
            
            if(
                $width > $_GET['windowWidth']
                && $area < $area_limit
            ){
                $im->readImageBlob($image_data);
                $im->resizeImage($_GET['windowWidth'], 0, imagick::FILTER_LANCZOS, 1);
                $image_data = $im->getImageBlob();
            }else if(
                $width <= ($_GET['windowWidth'] / 2)
                && $area < $area_limit
            ){

                
                $im->readImageBlob($image_data);
                $im->resizeImage(($width * 2), 0, imagick::FILTER_LANCZOS, 1);
                $image_data = $im->getImageBlob();
            }
        }

        if(
            $mime_array[0] == 'image'
            && (
                !isset($return_blob) //This was added so that we can use the same model for thumbnail generation
                || $return_blob !== true
            )
        ){
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