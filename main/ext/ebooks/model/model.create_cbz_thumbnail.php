<?php
/**
 * This model combines the get_cbz_image_data model and the create_thumbnail_from_image_data model
 * The first model is designed to take a database record as well as a page number and turn it into image data
 */

 $item = load_model('get_item_by_id', ['id' => $id], 'ebooks');

 if(empty($item) || ( isset($item['error']) && $item['error'] == true)){
    return [
        'error' => true,
        'message' => 'No database record found at provided ID'
    ];
 }

 $thumbDir = $_SESSION['thumb_dir'];

 if(empty($thumbDir)){
    return [
        'error' => true,
        'message' => 'No thumbnail directory set'
    ];
 }

 if(!file_exists($thumbDir)){
	if(empty($thumbDir)){
		$thumbDir = ROOT . DIRECTORY_SEPARATOR . 'upload' . DIRECTORY_SEPARATOR . 'thumbs';
	}
	if(!file_exists($thumbDir)){
		mkdir($thumbDir);
	}
}
$thumbDir .= DIRECTORY_SEPARATOR;

 //Early out if this isn't a CBZ record
 if($item['data_type'] != 'cbz'){
    return [
        'error' => true,
        'message' => 'create_cbz_thumbnail model can only create thumbnails for cbz files'
    ];
 }

$image_data = load_model('get_cbz_image_data', [
    'item' => $item,
    'page' => 0, //Thumbnails should be of the first page.
    'return_blob' => true //This returns the binary image data rather than 
], 'ebooks');

//If we get an error, pass the error up to the controller.
if(
    !is_array($image_data) 
    || (
        isset($image_data['error'])
        && $image_data['error'] == true
    )
){
    return $image_data;
}

if(!isset($image_data['content'])){
    return [
        'error' => true,
        'message' => 'No image data was returned from get_cbz_image_data model'
    ];
}

$image_data = $image_data['content'];

$thumbnail_data = load_model('create_thumbnail_from_image_data', [
    'image_data' => $image_data,
    'target_width' => 100
], 'ebooks');

//If we get an error, pass the error up to the controller
if(
    !is_array($thumbnail_data)
    || (
        isset($thumbnail_data['error'])
        && $thumbnail_data['error'] == true
    )
){
    return $thumbnail_data;
}

load_class('filesystem');
$fs = new filesystem;

if(empty($thumbnail_data['mime_type'])){
    return [
        'error' => true,
        'message' => 'No mimetype was returned for the image'
    ];
}

$file_extension = $fs->mime2ext($thumbnail_data['mime_type']);

if(empty($file_extension)){
    return [
        'error' => true,
        'message' => 'No extension could be linked to return mimetype of: '.$thumbnail_data['mime_type']
    ];
}

$thumbnail_name = $item['data_id'].'--'.basename($item['data_content']);
$thumbnail_name = explode('.', $thumbnail_name);
array_pop($thumbnail_name); //Remove the extension
$thumbnail_name = implode('.', $thumbnail_name);

$thumbnail_name .= '.'.$file_extension;

$thumbnail_name = $thumbDir . $thumbnail_name;

file_put_contents($thumbnail_name, $thumbnail_data['image_data']);

$clerk = new clerk;

$clerk->updateMetas($id, [
    'poster' => $thumbnail_name
]);

$thumb_url = build_slug(str_replace(ROOT, '', $thumbnail_name));

return [
    'thumbnail' => $thumb_url,
];