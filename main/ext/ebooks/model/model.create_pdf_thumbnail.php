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
 if($item['data_type'] != 'pdf'){
    return [
        'error' => true,
        'message' => 'create_pdf_thumbnail model can only create thumbnails for pdf files'
    ];
 }

load_class('pdf_image_helper', 'ebooks');

$pdfh = new pdf_image_helper;

$file_extension = '.jpg';

$thumbnail_name = $item['data_id'].'--'.basename($item['data_content']);
$thumbnail_name = explode('.', $thumbnail_name);
array_pop($thumbnail_name); //Remove the extension
$thumbnail_name = implode('.', $thumbnail_name);

$thumbnail_name .= '.'.$file_extension;

$thumbnail_name = $thumbDir . $thumbnail_name;

load_class('cli');

$check = $pdfh->genPdfThumbnail($item['data_content'], $thumbnail_name, 100);

if($check === false){
    return [
        'error' => true,
        'message' => 'Unable to generate thumbnail (error in pdf helper)'
    ];
}else{
    $check = file_exists($thumbnail_name);
    if($check === false){
        return [
            'error' => true,
            'message' => 'Unable to generate thumbnail (file did not exist)'
        ];
    }
}

$clerk = new clerk;

$clerk->updateMetas($id, [
    'poster' => $thumbnail_name
]);

$thumb_url = build_slug(str_replace(ROOT, '', $thumbnail_name));

return [
    'error' => false,
    'thumbnail' => $thumb_url,
];