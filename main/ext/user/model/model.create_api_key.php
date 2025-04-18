<?php

$clerk = new clerk;
global $user_model;

//Generate random data using default php functions
$seed_data = random_bytes(32);
$api_key = hash('sha256', $seed_data);

$clerk->addRecord(
    [
        'type' => 'api_key',
        'user_key' => $user_model->user_key,
        'content' => $api_key
    ]
);

return true;