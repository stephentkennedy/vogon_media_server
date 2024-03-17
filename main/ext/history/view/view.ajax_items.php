<?php

if(empty($search_results)){
    echo 'We couldn&#39;t find anything matching that search.';
	die();
}

$page_data['ajax'] = true;
$pageination = load_view('pageination', $page_data, 'audio');

$table = '<article id="ebook-library-main" class="flex results">
<div class="result-row result-header flex-row">
    <span class="result-one">Title</span>
    <span class="result-two">Type</span>
    <span class="result-three">Date</span>
    <span class="result-four">Link</span>
    <span class="result-five"></span>
</div>';
foreach($search_results as $r){
    $table .= '<div class="result-row flex-row" data-id="'.$r['data_id'].'">';

    if(empty($r['data_name'])){

        $array = explode('/', $r['data_content']);

        $r['data_name'] = array_pop($array);
    }

    $table .= '<span class="result-one">'.$r['data_name'].'</span>';
    $table .= '<span class="result-two">'.$r['data_type'].'</span>';
    $table .= '<span class="result-three">'.nice_date($r['last_edit']).'</span>';

    $link = '';
    switch($r['data_type']){
        case 'cbz':
        case 'pdf':
        case 'epub':
            $link = build_slug('view/'.$r['data_id'], [], 'ebooks');
            break;
        case 'audio':
            $link = build_slug('album/'.$r['data_parent'], [], 'audio');
            break;
        case 'movie':
        case 'tv':
            $link = build_slug('view/'.$r['data_id'], [], 'media');
            break;
    }

    $table .= '<span class="result-four"><a href="'.$link.'" class="button">View</a></span>';
    $table .= '<span class-"result-five"></span>';
    $table .= '</div>';
}

echo $pageination.$table.$pageination;