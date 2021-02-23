<header><h1>Server Tools</h1></header> 
<h2>App Identity</h2>
<?php echo NAME.' version '.VER; ?>
<h2>Mini DLNA Server</h2>
<a class="button" href="<?php echo build_slug('server/restart'); ?>">Restart</a>
<a class="button" href="<?php echo build_slug('server/start'); ?>">Start</a>
<a class="button" href="<?php echo build_slug('server/stop'); ?>">Stop</a>
<h2>Mass Import</h2>
<a class="button" href="<?php echo build_slug('server/import'); ?>">Import</a>
<h2>Tools</h2>
<a class="button" href="<?php echo build_slug('convert_audio', []); ?>" title="Web browsers only understand a small number of audio formats, this tool will attempt to use ffmpeg to convert common audio formats down to .mp3 which is understood by most browsers. This will delete the original files if successful so be sure you have backups.">Convert Audio Files</a>
<h2>Server Information</h2>
<h3>File System</h3>
<div id="filesystem">
<i class="fa fa-cog fa-spin fa-fw"></i>
</div>
<h3>Uptime <span id="status"></span></h3>
<div id="serverprocesses">
<i class="fa fa-cog fa-spin fa-fw"></i>
</div>
<script type="text/javascript">
	$(document).ready(function(){
		$.post(<?php echo "'".build_slug('ajax/ajax_drives_status/media')."'"; ?>, function(returned){
			var string = '<pre>' + returned + '</pre>';
			$('#filesystem').html(string);
		});
		uptime();
		setInterval(uptime, 5000);
	});
	function uptime(){
		$('#status').html('<i class="fa fa-cog fa-spin fa-fw"></i>');
		$.post(<?php echo "'".build_slug('ajax/ajax_top/media')."'"; ?>, function(returned){
			var string = '<pre>' + returned + '</pre>';
			$('#serverprocesses').html(string);
			$('#status').html('');
		});
	}
</script>