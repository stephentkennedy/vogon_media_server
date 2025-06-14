<?php
/**
 * This model should receive an image blob, that we'll then use imagick to adjust, if we can.
 * That blob will be on the $image_data variable
 */

if(empty($image_data)){
    return [
        'error' => true,
        'message' => 'No image blob provided on $image_data variable'
    ];
}

//Get our Mime Type
$file_info = new finfo(FILEINFO_MIME);
$mime = $file_info->buffer($image_data);
$mime = explode(';', $mime)[0];
$mime_array = explode('/', $mime);

//Error out if we can't identify it as an image
if($mime_array[0] != 'image'){
    return [
        'error' => true,
        'message' => 'Binary blob provided on $image_data cannot be identified as an image'
    ];
}

//Set our default width if one is not provided
if(empty($target_width)){
    $target_width = 100;
}

$im = new \Imagick();

$area_limit = \Imagick::getResourceLimit(\Imagick::RESOURCETYPE_AREA);

list($width, $height, $type, $attr) = getimagesizefromstring($image_data);

$area = $width * $height;

//Early out if we can't do anything to the image
if($area >= $area_limit){
    return [
        'error' => true,
        'message' => 'Provided image is too large for Imagick to manipulate'
    ];
}

//Calculate height ratio and target height based on image
if(empty($height)){
    return [
        'error' => true,
        'message' => 'Unable to calculate height'
    ];
}
$ratio = $width / $height;
$target_height = $ratio * $target_width;

$target_area = $target_width * $target_height;

//Early out if we're asked to make something too big.
if($target_area >= $area_limit){
    return [
        'error' => true,
        'message' => 'Target image size is too large for Imagick to manipulate'
    ];
}

$im->readImageBlob($image_data);
try{
    $im->resizeImage($target_width, 0, imagick::FILTER_LANCZOS, 1);
}catch(ImagickException $e){
    return [
        'error' => true,
        'message' => 'Unable to resize image'
    ];
}

$image_data = $im->getImageBlob();

return [
    'error' => false,
    'image_data' => $image_data,
    'mime_type' => $mime
];