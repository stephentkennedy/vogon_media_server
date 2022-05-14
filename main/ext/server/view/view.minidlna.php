<header><h1>Server Tools</h1></header>
<div class="row">
	<div class="col col-three">
		<h2>App Identity</h2>
		<?php echo NAME.' version '.VER; ?><br><br>
		<a class="button" href="<?php echo build_slug('update', [], 'server'); ?>">Check For Vogon Updates</a>
	</div>
	<div class="col col-three">
		<h2>Mini DLNA Server</h2>
		<a class="button" href="<?php echo build_slug('restart', [], 'server'); ?>">Restart</a>
		<a class="button" href="<?php echo build_slug('start', [], 'server'); ?>">Start</a>
		<a class="button" href="<?php echo build_slug('stop', [], 'server'); ?>">Stop</a>
	</div>
	<div class="col col-three">
		<h2>Server Controls</h2>
		<a class="button" href="<?php echo build_slug('check-for-updates', [], 'server'); ?>">Check For Software Updates</a><br><br>
		<a class="button" href="<?php echo build_slug('server-restart', [], 'server'); ?>">Restart Server</a>
		<a class="button" href="<?php echo build_slug('server-shutdown', [], 'server'); ?>">Shut Down Server</a>
	</div>
	<div class="col col-three">
		<h2>Import Media</h2>
		<a class="button" href="<?php echo build_slug('server/import'); ?>">Import</a>
	</div>
	<div class="col col-seven">
		<h2>Tools</h2>
		<a class="button" href="<?php echo build_slug('convert_audio', []); ?>" title="Web browsers only understand a small number of audio formats, this tool will attempt to use ffmpeg to convert common audio formats down to .mp3 which is understood by most browsers. This will delete the original files if successful so be sure you have backups.">Convert Audio Files</a> 
		<a class="button" href="<?php echo build_slug('server/flush_cache'); ?>" title="This will remove all cached data used to assemble pages that are known to take a long time to build. If you are making changes but not seeing them use this tool to empty the cache.">Flush Data Cache</a> 
		<a class="button" href="<?php echo build_slug('cleanup_data_table', [], 'server'); ?>" title="This may take several minutes to more than an hour depending on the size of your database. It's best to do this and walk away while leaving the browser running.">Cleanup Data Table</a> 
		<a class="button" href="<?php echo build_slug('cleanup_data_meta_table', [], 'server'); ?>" title="This table can be larger than your Data table by 10x or more, so this process may take several minutes to more than an hour depending on the size of your database. It's best to do this and walk away while leaving the browser running.">Cleanup Data Meta Table</a> 
		<a class="button" href="<?php echo build_slug('build'); ?>" title="Build a deployable .zip file of Vogon">Build</a>
		<a class="button" href="<?php echo build_slug('server/find_orphan_entries'); ?>" title="This will find all entries in your database that are no longer connected to files and mark them so that other tools can attempt to repair them.">Find Orphan Entries</a>
		<a class="button" href="<?php echo build_slug('server/patch_series'); ?>" title="Did you import a series while the series importer was broken and it generated a new series entry for each episode? This will fix that.">Merge Series</a>
		<a class="button" href="<?= build_slug('server/ebook_series_type_change'); ?>" title="When ebooks were first introduced, they used the same series type as videos, this will reassign them to a new type.">Ebook Series Cleanup</a>
	</div>
	<div class="col col-ten">
		<h2>Server Information</h2>
		<h3>Monitor <span id="status"></span></h3>
		<div id="serverprocesses">
		<i class="fa fa-cog fa-spin fa-fw"></i>
		</div>
		<h3>Drives</h3>
		<div id="filesystem">
		<i class="fa fa-cog fa-spin fa-fw"></i>
		</div>
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function(){
		$.post(<?php echo "'".build_slug('ajax/ajax_drives_status/media')."'"; ?>, function(returned){
			var string = '' + returned + '';
			$('#filesystem').html(string);
		});
		uptime();
		setInterval(uptime, 5000);
	});
	function uptime(){
		$('#status').html('<i class="fa fa-cog fa-spin fa-fw"></i>');
		$.post(<?php echo "'".build_slug('ajax/ajax_top/media')."'"; ?>, function(returned){
			var string = '' + returned + '';
			$('#serverprocesses').html(string);
			$('#status').html('');
		});
	}
</script>
