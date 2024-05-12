<?php 
	if(!isset($genres)){
		$genres = [];
	}
	if(!isset($title)){
		$title = '';
	}
	if(!isset($genre)){
		$genre = false;
	}
	if(!isset($director)){
		$director = '';
	}
	if(!isset($release)){
		$release = '';
	}
	if(!isset($starring)){
		$starring = '';
	}
	if(!isset($desc)){
		$desc = '';
	}
	if(!isset($location)){
		$location = '';
	}
	if(!isset($poster)){
		$poster = '';
	}
	if(!isset($length)){
		$length = 0;
	}
	if(!isset($series)){
		$series = '';
	}
?>
<header><h1><?php echo $title; ?><?php if(isset($id)){ ?> <a title="Back" class="fa fa-arrow-left" href="<?php echo build_slug('view/'.$id, [], 'media'); ?>"></a> <a title="Remove" class="open-popup fa fa-times" data-title="Remove <?php echo $title; ?>" data-src="<?php echo build_slug('ajax/delete_confirm/media', ['id' => $id]); ?>"></a><?php } ?></h1></header>
<form method="post">
	<input type="hidden" name="action" value="save-film-meta">
	<?php if(isset($id)){
		echo '<input type="hidden" name="id" value="'.$id.'">';
	} ?>
	<label for="title">Title <button class="button" id="search_tmdb" type="button"><i class="fa fa-search" ></i> Search TMDB</button></label>
	<input id="title" type="text" name="title" value="<?php echo $title; ?>">
	<label for="series">Series</label>
	<input id="series" name="series" type="text" value="<?php echo $series; ?>">
	<label for="director">Director</label>
	<input type="text" id="director" name="director" value="<?php echo $director; ?>">
	<label for="release">Year of Release</label>
	<input type="text" id="release" name="release" value="<?php echo $release; ?>">
	<label for="starring">Starring</label>
	<input type="text" id="starring" name="starring" value="<?php echo $starring; ?>">
	<label for="desc">Description</label>
	<textarea id="desc" name="desc"><?php echo $desc; ?></textarea>
	<input type="checkbox" value="1" id="animorphic" name="animorphic" <?php if(!empty($animorphic)){ echo 'checked'; } ?>> <label class="inline" for="animorphic">Vertical Stretch (Animorphic Widescreen Fix)?</label>
	<label for="runtime">Runtime (In Minutes)</label>
	<small>On files smaller than 2GB the server will attempt to calculate a runtime from the file&#39;s meta data. If that fails, the server will then attempt to get the data using FFMPEG, but if that also fails, this number will be used.</small><br><br>
	<input id="runtime" type="number" name="runtime" value="<?php echo $length / 60; ?>">
	<?php load_controller('ajax_filebrowser', ['b_file' => $location, 'form' => 'file'], 'filebrowser'); ?>
	<button type="submit" class="button"><i class="fa fa-floppy-o"></i> Save</button>
</form>
<style type="text/css">
	.popup .result{
		border: 1px solid #222222;
		padding: 5px;
		transition: background 0.2s linear;
		background: rgba(0,0,0,0);
	}
	.popup .result:hover{
		background: rgba(0,0,0,0.2);
		cursor: pointer;
	}
	.popup .result + .result{
		margin-top: 10px;
	}
</style>
<script type="text/javascript">
	$(document).ready(function(){
		$('#series').autocomplete({
			source: function(request, response){
				$.get(<?php echo "'".build_slug('ajax/ajax_search_series/media')."'"; ?>, {
					'search': request.term
				}).done(function(data){
					response($.map(data, function(item){
						return{
							label: item.data_name,
							value: item.data_name
						}
					}));
				});
			}
		});
		$('#search_tmdb').click(function(){
			var search = $('#title').val();
			$.get(<?php echo "'".build_slug('ajax/ajax_tmdb_results/media')."'"; ?>, {
				'search': search
			}).done(function(data){
				//console.log(data);
				var string = '';
				window.tmdb_results = {};
				for(var i in data){
					var entry = data[i];
					window.tmdb_results[i] = entry;
					string += '<div class="result" data-id="'+i+'"><h3>'+entry.title+' ('+entry.date+')</h3><p>'+entry.desc+'</p></div>';
				}

				var w = aPopup.newWindow(string, {title: 'Results'});

				w.find('div.result').click(function(){
					var $this = $(this);
					var selected = $this.data('id');
					selected = window.tmdb_results[selected];
					$('#title').val(selected.title);
					$('#release').val(selected.date);
					$('#desc').val(selected.desc);
					w.remove();
				});
			});
		});
	});
</script>