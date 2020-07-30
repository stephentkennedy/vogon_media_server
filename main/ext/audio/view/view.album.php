<?php 
$default_meta = [
	'artist' => '',
	'composer' => '',
	'genre' => '',
	'year' => '',
	'track' => '',
	'length' => 0
];
?><header><h1><?php echo $album['data_name']; ?></h1></header>
<div class="container">
<button class="button play-all" data-tracks="<?php 
$temp = [];
foreach($members as $m){ $temp[] = (int)$m['data_id']; }
echo json_encode($temp); ?>"><i class="fa fa-play"></i> Play All</button>
<table>
	<thead>
		<tr>
			<th>Track</th>
			<th>Name</th>
			<th>Length</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		<?php
			foreach($members as $m){
				foreach($default_meta as $key => $default){
					if(empty($m['meta'][$key])){
						$m['meta'][$key] = $default;
					}
				}
				echo '<tr>';
				echo '<td>'.$m['meta']['track'].').</td>';
				echo '<td>'.$m['data_name'].'</td>';
				echo '<td>'.formatLength($m['meta']['length']).'</td>';
				echo '<td>';
				echo '<a class="button miniplayer-play" data-id="'.$m['data_id'].'"><i class="fa fa-play"></i></a>';
				echo '<a class="button playlist-add" data-id="'.$m['data_id'].'"><i class="fa fa-plus"></i></a>';
				echo '<a class="button" href="'.build_slug('edit/'.$m['data_id'], [], 'audio').'"><i class="fa fa-pencil"></i></a>';
				echo '</td>';
				echo '</tr>';
			}
		?>
	</tbody>
</table>
</div>
<?php echo load_view('mini_player', [], 'audio'); ?>
<script type="text/javascript">
	$(document).ready(function(){
		$('.play-all').click(function(){
			var list = $(this).data('tracks');
			playlist.list = list;
			playlist.play(0);
		});
	});
</script>