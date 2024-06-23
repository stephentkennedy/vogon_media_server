<?php
$series = $_POST['series'];
$sub_series = $_POST['sub_series'];
$members = $_POST['members'];

if(empty($series) || empty($members)){
    return;
}

$clerk = new clerk;

$series_record = $clerk->getRecord([
    'name' => $series,
    'type' => 'ebook_series'
]);

if(empty($series_record)){
    $record = [
        'name' => $series,
        'type' => 'ebook_series'
    ];

    $series_id = $clerk->addRecord($record);
}else{
    $series_id = $series_record['data_id'];
}

//Need to figure out this logic
foreach($members as $key => $item){
    $meta_data = [
        'order' => $key
    ];
    if(!empty($sub_series)){
        $meta_data['sub_series'] = $sub_series;
    }
    $clerk->updateMetas($item, $meta_data);

    $clerk->updateRecord([
        'parent' => $series_id
    ], $item);
}

return [
    'success' => true
];