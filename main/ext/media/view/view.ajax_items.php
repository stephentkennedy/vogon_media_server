<?php

switch($format){
	case 'JSON':
		//Shouldn't be in this branch for video. At least for now
		$output = [
			'page_data' => $page_data,
			'items' => []
		];
		foreach($search_results as $r){
			if(empty($r['data_name'])){
				$filename = $r['data_content'];
				$filename = explode(DIRECTORY_SEPARATOR, $filename);
				$r['data_name'] = array_pop($filename);
			}
			if(empty($r['meta']['artist'])){
				$artist = '[Unknown]';
			}else{
				$artist = $r['meta']['artist'];
			}
			if(empty($r['meta']['length'])){
				$length = '[Unknown]';
			}else{
				$length = formatLength($r['meta']['length']);
			}
			if(empty($r['meta']['genre'])){
				$genre = '[Unknown]';
			}else{
				$genre = $r['meta']['genre'];
			}
			$output['items'][] = [
				'id' => $r['data_id'],
				'name' => $r['data_name'],
				'file' => $r['data_content'],
				'album' => $r['album'],
				'artist' => $artist,
				'length' => $length,
				'genre' => $genre
			];
		}
	
		header('Content-Type: application/json;charset=utf-8');
		echo json_encode($output);
	
		break;
	case 'HTML':
		$page_data['ajax'] = true;
		$pageination = load_view('pageination', $page_data, 'audio');
		$table = '<div  class="video-contain">';
		foreach($search_results as $r){
			//debug_d($r);
			if(empty($r['series'])){
				$r['series'] = '';
			}
			if(empty($r['meta']['release'])){
				$r['meta']['release'] = '';
			}
			if(empty($r['data_name'])){
				$filename = $r['data_content'];
				$filename = explode(DIRECTORY_SEPARATOR, $filename);
				$r['data_name'] = array_pop($filename);
			}
			if($r['data_type'] == 'video'){
				$class = 'movie';
			}else{
				$class = 'tv-series';
			}
			if(!empty($r['meta']['poster'])){
				$r['meta']['poster'] = str_replace(ROOT, '', $r['meta']['poster']);
				$table .= '<a href="'.build_slug('view/'.$r['data_id'], [], 'media').'" class="video-preview '.$class.'"><div class="img-contain"><img src="'.URI.$r['meta']['poster'].'"></div><h4>'.$r['data_name'].' ('.$r['meta']['release'].')</h4></a>';
			}else{
				$table .= '<a href="'.build_slug('view/'.$r['data_id'], [], 'media').'" class="video-preview '.$class.'"><div class="img-contain"><i class="fa fa-play-circle"></i></div><h4>'.$r['data_name'].' ('.$r['meta']['release'].')</h4></a>';
			}
		}
		$table .= '</div>';
		echo $pageination.$table.$pageination;
		break;
}