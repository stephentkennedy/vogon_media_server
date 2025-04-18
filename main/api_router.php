<?php

$ext = get_slug_part('ext');

//Confirm the extension is loaded and has an API controller, exit early with error if not.
if(
    !in_array($ext, $_SESSION['loaded_extensions'])
    || !file_exists(ext_root($ext).'controller' . DIRECTORY_SEPARATOR . 'controller.api.php')
){
    http_response_code(404);
    $to_return = [
        'error' => true,
        'message' => 'No Route',
        'ext' => $ext
    ];

    echo load_view('json', $to_return);
    return;
}

load_controller('api', [], $ext);