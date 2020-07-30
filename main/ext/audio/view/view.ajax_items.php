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
		$pageination = load_view('pageination', $page_data, 'audio');
		$table = '<table><thead><tr>
			<th>Title</th>
			<th>Album</th>
			<th>Artist</th>
			<th>Length</th>
			<th></th>
		</tr></thead><tbody>';
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
			$table .= '<tr>';
			$table .= '<td>'.$r['data_name'].'</td>';
			if(!empty($r['album'])){
				$table .= '<td><a href="'.build_slug('album/'.$r['data_parent'],[],'audio').'">'.$r['album'].'</a></td>';
			}else{
				$table .= '<td>[Unknown]</td>';
			}
			$table .= '<td>'.$artist.'</td>';
			$table .= '<td>'.$length.'</td>';
			$table .= '<td>';
			$table .= '<a class="button miniplayer-play" data-id="'.$r['data_id'].'"><i class="fa fa-play"></i></a>';
			$table .= '<a class="button playlist-add" data-id="'.$r['data_id'].'"><i class="fa fa-plus"></i></a>';
			$table .= '<a class="button" href="'.build_slug('edit/'.$r['data_id'], [], 'audio').'"><i class="fa fa-pencil"></i></a>';
			$table .= '</td>';
			$table .= '</tr>';
		}
		$table .= '</tbody></table>';
		echo $pageination.$table.$pageination;
		break;
}