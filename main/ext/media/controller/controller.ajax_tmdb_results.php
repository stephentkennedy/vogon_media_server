<?php

load_class('tmdb_wrapper');

$error = [
    'error' => true,
    'message' => 'No API Key is set'
];

$tmdb = new tmdb_wrapper;

if($tmdb->api == false){
    echo load_view('json', $error);
    return;
}

$error = [
    'error' => true,
    'message' => 'A title must be set to search The Movie Database'
];

if(empty($_GET['search'])){
    echo load_view('json', $error);
    return;
}

$records = $tmdb->api->searchMovie($_GET['search']);

if(count($records) > 0){
    $to_return = [];
    foreach($records as $record){
        $to_return[$record->getID()] = [
            'title' => $record->getTitle(),
            'desc' => $record->get('overview'),
            'date' => date('Y', strtotime($record->get('release_date'))),
            'cast' => $record->getCast(),
            'genre' => $record->getGenres()
        ];
    }
    echo load_view('json', $to_return);
    return;
}else{
    $error = [
        'error' => true,
        'message' => 'No results found.'
    ];
    echo load_view('json', $error);
    return;
}