<?php
$meta_data = [
	'artist' => '',
	'genre' => '',
	'composer' => '',
	'year' => '',
	'track' => ''
];
foreach($meta_data as $item => $default){
	if(empty($meta[$item])){
		$meta[$item] = $default;
	}
}
?><header><h1>Edit: <?php echo $data_name; ?></h1></header>
<form method="post">
	<a class="button miniplayer-play" data-id="<?php echo $data_id;?>"><i class="fa fa-play"></i> Play</a>
	<input type="hidden" name="action" value="edit">
	<input type="hidden" name="id" value="<?php echo $data_id;?>">
	<label for="data_name">Name</label>
	<input id="data_name" type="text" name="data_name" value="<?php echo $data_name; ?>">
	<label for="data_content">Location</label>
	<input id="data_content" type="text" name="data_content" readonly disabled value="<?php echo $data_content; ?>">
	<label for="meta_artist">Artist</label>
	<input id="meta_artist" type="text" name="meta_artist" value="<?php echo $meta['artist']; ?>">
	<label for="meta_album">Album</label>
	<input id="meta_album" type="text" name="meta_album" value="<?php echo $album; ?>">
	<label for="meta_genre">Genre</label>
	<input id="meta_genre" type="text" name="meta_genre" value="<?php echo $meta['genre']; ?>">
	<label for="meta_composer">Composer</label>
	<input id="meta_composer" type="text" name="meta_composer" value="<?php echo $meta['composer']; ?>">
	<label for="meta_year">Year</label>
	<input id="meta_year" type="text" name="meta_year" value="<?php echo $meta['year']; ?>">
	<label for="meta_track">Track Number</label>
	<input id="meta_track" type="text" name="meta_track" value="<?php echo $meta['track']; ?>">
	<button type="submit"><i class="fa fa-floppy-o"></i> Save</button>
</form>
<style>
	.miniplayer-play{
		display: inline-block;
		margin-bottom: 10px;
	}
</style>
<?php echo load_view('mini_player', [], 'audio'); ?>
<script type="text/javascript">
	$(document).ready(function(){
		$('#meta_album').autocomplete({
			source: function(request, response){
				$.get('/ajax/ajax_album/audio', {'search': request.term}).done(function(data){
					response($.map(data, function(item){
						return{
							label: item.data_name,
							value: item.data_name
						}
					}));
				});
			}
		});
		$('#meta_artist').autocomplete({
			source: function(request, response){
				$.get('/ajax/ajax_artist/audio', {'search': request.term}).done(function(data){
					response($.map(data, function(item){
						return{
							label: item.data_meta_content,
							value: item.data_meta_content
						}
					}));
				});
			}
		});
		$('#meta_genre').autocomplete({
			source: function(request, response){
				$.get('/ajax/ajax_genre/audio', {'search': request.term}).done(function(data){
					response($.map(data, function(item){
						return{
							label: item.data_meta_content,
							value: item.data_meta_content
						}
					}));
				});
			}
		});
		$('#meta_composer').autocomplete({
			source: function(request, response){
				$.get('/ajax/ajax_composer/audio', {'search': request.term}).done(function(data){
					response($.map(data, function(item){
						return{
							label: item.data_meta_content,
							value: item.data_meta_content
						}
					}));
				});
			}
		});
	});
</script>