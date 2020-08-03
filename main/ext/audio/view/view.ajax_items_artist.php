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
			<th>Artist</th>
			<th></th>
		</tr></thead><tbody>';
		foreach($search_results as $r){
			$table .= '<tr>';
			$table .= '<td>'.$r['data_meta_content'].'</td>';
			$table .= '<td>';
			$table .= '<a class="button" href="'.build_slug('artist/'.$r['data_meta_id'], [], 'audio').'">View</a>';
			$table .= '</td>';
			$table .= '</tr>';
		}
		$table .= '</tbody></table>';
		echo $pageination.$table.$pageination;
		break;
}