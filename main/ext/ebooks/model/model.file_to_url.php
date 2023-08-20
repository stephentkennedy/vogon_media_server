<?php

$url = $item['data_content'];

$url = str_replace(ROOT, '', $url);

if(!function_exists('myURLEncode')){
    function myUrlEncode($string) {
        return $string;
        $entities = array('%20', '%21', '%2A', '%27', '%28', '%29', '%3B', '%3A', '%40', '%26', '%3D', '%2B', '%24', '%2C', '%2F', '%3F', '%25', '%23', '%5B', '%5D');
        $replacements = array('!', '*', "'", "(", ")", ";", ":", "@", "&", "=", "+", "$", ",", "/", "?", "%", "#", "[", "]");
        return str_replace($entities, $replacements, urlencode($string));
    }
}

/*$url = build_slug('serve/'.$url, [], 'ebooks');

$prefix = $_SERVER['HTTP_HOST'];

$prefix = 

$url = $prefix . $url;

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

$url = $protocol.$url .'/';*/

$sections = explode('/', $url);

foreach($sections as $key => $section){
    $sections[$key] = myUrlEncode($section);
}

$url = implode('/', $sections);

return [
    'url' => $url
];