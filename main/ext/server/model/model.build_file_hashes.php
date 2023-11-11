<?php
set_time_limit(60);
$clerk = new clerk;
$record = $clerk->getRecord([
    'id' => $row['data_id']
], true);

$media_types = load_model('get_media_types', [], 'server');

if(empty($record)){
    return '[Unknown ID]';
}else if(
    in_array($record['data_type'], $media_types)
    && empty($record['meta']['file_hash'])
){
    $file_to_check = $record['data_content'];
    $check = file_exists($file_to_check);
    if(!$check){
        return '[File Does Not Exist]';
    }

    $hash = hash_file('sha256', $file_to_check);

    $clerk->updateMetas($row['data_id'], [
        'file_hash' => $hash
    ]);

    return $record['data_name'].' - '.$hash;
}else{
    return '[Invalid Data Type]';
}