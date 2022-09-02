<?php
global $user;
switch($format){
    case 'JSON':
        break;
    case 'HTML':
        if(empty($search_results)){
			echo 'We couldn&#39;t find anything matching that search.';
			die();
		}
        $page_data['ajax'] = true;
        $pageination = load_view('pageination', $page_data, 'audio');
        $table = '<article id="ebook-library-main" class="flex results">
        <div class="result-row result-header flex-row">
            <span class="result-one">Title</span>
            <span class="result-two">Series</span>
            <span class="result-three">Sub Series</span>
            <span class="result-four">Author</span>
            <span class="result-five"></span>
        </div>';
        foreach($search_results as $r){
            if(empty($r['parent_data_name'])){
				$r['parent_data_name'] = '';
			}
			if(empty($r['data_name'])){
				$filename = $r['data_content'];
				$filename = explode(DIRECTORY_SEPARATOR, $filename);
				$r['data_name'] = array_pop($filename);
			}
			if(empty($r['author'])){
				$artist = '[Unknown]';
			}else{
				$artist = $r['author'];
			}
			if(empty($r['length'])){
				$length = '[Unknown]';
			}else{
				$length = formatLength($r['length']);
			}
			if(empty($r['genre'])){
				$genre = '[Unknown]';
			}else{
				$genre = $r['genre'];
			}
            if(empty($r['sub_series'])){
                $sub_series = '[Unknown]';
            }else{
                $sub_series = $r['sub_series'];
            }
            if($r['data_type'] == 'pdf'){
                $icon = 'file-pdf-o';
            }else{
                $icon = 'newspaper-o';
            }
            if(!empty($r['history'])){
                $history = ' &mdash; [Read]';
            }else{
                $history = '';
            }
            $table .= '<div class="result-row flex-row" data-id="'.$r['data_id'].'">';
            $table .= '<span class="result-one"><i class="fa fa-'.$icon.'"></i>&nbsp;'.$r['data_name'].$history.'</span>';
            if(!empty($r['parent_data_name'])){
                $table .= '<span class="result-two"><a href="">'.$r['parent_data_name'].'</a></span>';
            }else{
                $table .= '<span class="result-two">[Unknown]</span>';
            }
            $table .= '<span class="result-three">'.$sub_series.'</span>';
            $table .= '<span class="result-four">'.$artist.'</span>';
            $table .= '<span class="result-five">';
            $link = build_slug('view/'.$r['data_id'], [], 'ebooks');
            if($r['data_type'] == 'pdf'){
                $link .= '?file=';
                $replace = [
                    ROOT,
                    '#'
                ];
                $with = [
                    '',
                    urlencode('#')
                ];
                $link .= str_replace($replace, $with, $r['data_content']);
            }
            $table .= '<a class="button" href="'.$link.'" title="View"><i class="fa fa-eye"></i></a>';
            $link = build_slug('edit/'.$r['data_id'], [], 'ebooks');           
            $table .= '<a class="button ajax-form" data-href="'.$link.'" data-id="'.$r['data_id'].'" title="Edit"><i class="fa fa-pencil"></i></a>';
            $table .= '</span>';
            $table .= '</div>';
        }
        $table .= '</article>';
        echo $pageination.$table.$pageination;
        break;
}