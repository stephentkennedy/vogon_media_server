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
ob_start();
?><form method="post">
	<input type="hidden" name="action" value="edit">
	<input type="hidden" name="id" value="<?php echo $data_id;?>">
	<label for="data_name">Name</label>
	<input id="data_name" type="text" name="data_name" value="<?php echo $data_name; ?>">
	<label for="data_content">Location</label>
	<input id="data_content" type="text" name="data_content" readonly disabled value="<?php echo $data_content; ?>">
	<label for="meta_artist">Artist</label>
	<input id="meta_artist" type="text" name="meta_artist" value="<?php echo $meta['artist']; ?>">
	<label for="meta_album">Album</label>
	<a class="button" href="<?php echo build_slug('album/'.$album_id, [], 'audio'); ?>" target="_blank"><i class="fa fa-eye"></i> <?php echo $album; ?></a><br><br>
	<input id="meta_album" type="text" name="meta_album" value="<?php echo $album; ?>">
	<label for="meta_genre">Genre</label>
	<input id="meta_genre" type="text" name="meta_genre" value="<?php echo $meta['genre']; ?>">
	<label for="meta_composer">Composer</label>
	<input id="meta_composer" type="text" name="meta_composer" value="<?php echo $meta['composer']; ?>">
	<label for="meta_year">Year</label>
	<input id="meta_year" type="text" name="meta_year" value="<?php echo $meta['year']; ?>">
	<label for="meta_track">Track Number</label>
	<input id="meta_track" type="text" name="meta_track" value="<?php echo $meta['track']; ?>">
	<button type="button" class="submit"><i class="fa fa-floppy-o"></i> Save</button>
</form>
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
</script><?php
$content = ob_get_clean();
$options = [
	'title' => 'Edit: '.$data_name,
	'width' => '50vw',
	'style' => 'top:100px;left:25vw;'
];
echo load_view('json', [
	'content' => $content,
	'options' => $options
]);