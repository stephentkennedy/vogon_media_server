<?php
$clerk = new clerk;

$check = $clerk->getRecord($id);
if(
    !empty($check)
    && $check['data_type'] == 'audio'
){
    $meta_data = [
        'level_graph' => json_encode($level_graph),
        'level_peak' => (int)$level_peak,
        'max_bin' => (int)$max_bin,
        'min_bin' => (int)$min_bin
    ];

    return $clerk->updateMetas($id, $meta_data);
}
return false;