<?php 
$default_meta = [
	'artist' => '',
	'composer' => '',
	'genre' => '',
	'year' => '',
	'track' => '',
	'length' => 0,
	'history' => false
];

?><header><h1><?php echo $album['data_name']; ?></h1></header>
<div class="container">
<a class="button play-all" data-tracks="<?php 
$temp = [];
foreach($members as $m){ $temp[] = (int)$m['data_id']; }
echo json_encode($temp); ?>"><i class="fa fa-play"></i> Play All</a><?php

if(!empty($album['meta']['history'])){
	echo ' <a class="album-resume button" data-id="'.$album['data_id'].'"><i class="fa fa-spin fa-cog"></i> ... Checking</a>';
}else{
	echo ' <a class="enable-history button" href="'.build_slug('album/'.$album['data_id'], ['action' => 'enable_history'], 'audio').'">Enable History Tracking</a>';
}

?>
<table style="margin-top: 15px;">
	<thead>
		<tr>
			<th>Track</th>
			<th>Name</th><?php
			if(!empty($album['meta']['history']) && $album['meta']['history'] == true){
				echo '<th>Listened</th>';
			}
			?><th>Length</th>
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
				if($m['meta']['history'] == true){
					//echo '<td>'.formatLength($m['listened']).'</td>';
					$percent = $m['listened'] / $m['meta']['length'];
					$percent = ceil($percent * 100);
					echo '<td><div class="length"><div class="percent" style="width: '.$percent.'%"></div></div></td>';
				}
				echo '<td>'.formatLength($m['meta']['length']).'</td>';
				echo '<td>';
				echo '<a class="button miniplayer-play" data-id="'.$m['data_id'].'"><i class="fa fa-play"></i></a>';
				echo '<a class="button playlist-add" data-id="'.$m['data_id'].'"><i class="fa fa-plus"></i></a>';
				echo '<a class="button ajax-form" data-href="'.build_slug('edit/'.$m['data_id'], [], 'audio').'"><i class="fa fa-pencil"></i></a>';
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
		$('.ajax-form').off().click(function(){
			if($(this).data('href') != ''){
				var data = {
					format: 'ajax_form'
				};
				$.get($(this).data('href'), data, function( returned ){
					app.ajax_form(returned);
				});
			}
		});
		var resume = $('.album-resume');
		if(resume.length > 0){
			var id = resume.data('id');
			$.get('<?php echo build_slug("ajax/ajax_get_next/audio"); ?>', {id: id}).done(function(returned){
				if(returned.result == true){
					resume.html(returned.text);
					var tracks = $('.play-all').data('tracks');
					var add = false;
					var a_tracks = [];
					for(i in tracks){
						if(tracks[i] == returned.id){
							add = true;
						}
						if(add == true){
							a_tracks.push(tracks[i]);
						}
					}
					resume.attr('data-tracks', JSON.stringify(a_tracks));
					resume.addClass('play-all');
					$('.play-all').off().click(function(){
						var list = $(this).data('tracks');
						playlist.list = list;
						playlist.play(0);
					});
				}else{
					var parent = resume.parent();
					resume.remove();
				}
			});
		}
	});
</script>
<style>
	.length{
		border: 1px solid var(--secondary-accent);
		border-radius: var(--border-radius);
		overflow: hidden;
		width: 100%;
		position: relative;
		height: 1rem;
		padding: 0;
	}
	.length > .percent{
		height: 1rem;
		background: var(--secondary-accent);
		position: relative;
	}
</style>