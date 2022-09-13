<?php
$write = $struct;
$write = json_encode($write);
$file_data = [
    'filename' => $filename.'.json',
    'ext' => 'installer',
    'content' => $write
];
$write_data = load_model('write_file', $file_data);
if(gettype($write_data) == 'bool'){
    return false;
}else if($write_data['success'] == false){
    return false;
}else{
    return $write_data['filename'];
}