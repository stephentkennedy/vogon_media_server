<header><h1>Audio Library</h1></header>
<?php echo load_view('pageination', $page_data, 'audio'); ?>
<table>
	<thead>
		<tr>
			<th>Title</th>
			<th>Album</th>
			<th>Artist</th>
			<th>Length</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
<?php
	foreach($audio_tracks as $a){
		
		if(empty($a['data_name'])){
			//$a['data_name'] = '<span class="bold">[Unknown]</span>';
			$filename = $a['data_content'];
			$filename = explode(DIRECTORY_SEPARATOR, $filename);
			$a['data_name'] = array_pop($filename);
		}
		if(empty($a['meta']['artist'])){
			$a['meta']['artist'] = '<span class="bold">[Unknown]</span>';
		}
		if(empty($a['meta']['length'])){
			$a['meta']['length'] = 0;
		}
		
		echo '<tr>';
		
		echo '<td>'.$a['data_name'].'</td>';
		if(!empty($a['data_parent'])){
			echo '<td>'.$albums[$a['data_parent']].'</td>';
		}else{
			echo '<td><span class="bold">[Unknown]</span></td>';
		}
		echo '<td>'.$a['meta']['artist'].'</td>';
		echo '<td>'.formatLength($a['meta']['length']).'</td>';
		
		//echo '<td><a href="'.build_slug('listen/'.$a['data_id'], [], 'audio').'" class="button"><i class="fa fa-play"></i></a></td>';
		
		echo '<td>';
		
		echo '<a class="button miniplayer-play" data-id="'.$a['data_id'].'"><i class="fa fa-play"></i></a>';
		echo '<a class="button playlist-add" data-id="'.$a['data_id'].'"><i class="fa fa-plus"></i></a>';
		
		echo '</td>';
		
		echo '</tr>';
	}?>
	</tbody>
</table>
<?php echo load_view('pageination', $page_data, 'audio'); ?>
<?php echo load_view('mini_player', [], 'audio'); ?>