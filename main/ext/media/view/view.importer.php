<header><h1>Mass Importer</h1></header>
<p><small>This importer will scan the selected directory and add any media that is not already in the database. Due to the intensive nature of this process, it can take several minutes to hours depending on the power of your server and the amount of files needed to be scanned.</small></p>
<form method="post">
	<input type="hidden" name="action" value="import">
	<label>Files to import</label>
	<label for="type-video"><input id="type-video" type="radio" name="type" value="video" checked> Video</label>
	<label for="type-audio"><input id="type-audio" type="radio" name="type" value="audio"> Audio</label>
	<label for="type-ebook"><input id="type-ebook" type="radio" name="type" value="ebook"> E-Book</label>
	<fieldset>
		<label>Series Name (optional)</label>
		<input id="series_id" type="hidden" name="series_id" value="0">
		<input id="series" type="text" name="series_name" value="" placeholder="Name">
	</fieldset><br>
	<?php load_controller('ajax_filebrowser', ['form' => 'dir'], 'filebrowser'); ?>
	<button type="submit"><i class="fa fa-check"></i> Import</button>
</form>
<script type="text/javascript">
	$('#series').autocomplete({
		source: function(request, response){
			$.get('<?php echo build_slug("ajax/ajax_series/media"); ?>', {'search': request.term}).done(function(data){
				response($.map(data, function(item){
					return{
						label: item.data_name,
						value: item.data_name,
						dataValue: item.data_id
					};
				}));
			});
		},
		select: function(event, ui){
			$('#series_id').val(ui.item.dataValue);
		},
		search: function(){
			$('#series_id').val('0');
		}
	});
</script>
