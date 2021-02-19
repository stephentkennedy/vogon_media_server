<?php

switch($format){
	case 'JSON':
	
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
		$shufflePlay = '<button class="button miniplayer-server-shuffle"><i class="fa fa-retweet"></i> Shuffle Play All</button><br><br>';
		$pageination = load_view('pageination', $page_data, 'audio');
		/*$table = '<table><thead><tr>
			<th>Title</th>
			<th>Album</th>
			<th>Artist</th>
			<th>Length</th>
			<th></th>
		</tr></thead><tbody>';*/
		$table = '<article id="audio-library-main" class="flex results">
			<div class="result-row result-header flex-row">
				<span class="result-one">Title</span>
				<span class="result-two">Album</span>
				<span class="result-three">Artist</span>
				<span class="result-four">Length</span>
				<span class="result-five"></span>
			</div>';
		foreach($search_results as $r){
			//debug_d($r);
			if(empty($r['album'])){
				$r['album'] = '';
			}
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
			//$table .= '<tr>';
			$table .= '<div class="result-row flex-row">';
			//$table .= '<td>'.$r['data_name'].'</td>';
			$table .= '<span class="result-one">'.$r['data_name'].'</span>';
			if(!empty($r['album'])){
				//$table .= '<td><a href="'.build_slug('album/'.$r['data_parent'],[],'audio').'">'.$r['album'].'</a></td>';
				$table .= '<span class="result-two"><a href="'.build_slug('album/'.$r['data_parent'],[],'audio').'">'.$r['album'].'</a></span>';
			}else{
				//$table .= '<td>[Unknown]</td>';
				$table .= '<span class="result-two">[Unknown]</span>';
			}
			//$table .= '<td>'.$artist.'</td>';
			$table .= '<span class="result-three">'.$artist.'</span>';
			//$table .= '<td>'.$length.'</td>';
			$table .= '<span class="result-four">'.$length.'</span>';
			//$table .= '<td>';
			$table .= '<span class="result-five">';
			$table .= '<a class="button miniplayer-play" data-id="'.$r['data_id'].'"><i class="fa fa-play"></i></a>';
			$table .= '<a class="button playlist-add" data-id="'.$r['data_id'].'"><i class="fa fa-plus"></i></a>';
			$table .= '<a class="button ajax-form" data-href="'.build_slug('edit/'.$r['data_id'], [], 'audio').'"><i class="fa fa-pencil"></i></a>';
			//$table .= '</td>';
			$table .= '</span>';
			//$table .= '</tr>';
			$table .= '</div>';
		}
		$table .= '</article>';
		echo $shufflePlay.$pageination.$table.$pageination;
		break;
}