<h2>Video Media Settings</h2>
<h4>Thumbnail Directory</h4>
<form action="?ext={{ext_name}}&form=thumb&force_reload=true" method="post">
	<?php
	
	if(empty($_SESSION['thumb_dir'])){
		$_SESSION['thumb_dir'] = ROOT . DIRECTORY_SEPARATOR . 'upload' . DIRECTORY_SEPARATOR . 'thumbs';
	}
	
	load_controller('ajax_filebrowser', [
		'form' => 'dir',
		'dir' => $_SESSION['thumb_dir'],
		'root' => ROOT
	], 'filebrowser'); ?><br><br>
	<button type="submit"><i class="fa fa-floppy-o"></i> Save</button>
</form>
<h4>History Sync (In Seconds)</h4>
<form action="?ext={{ext_name}}&form=mediahistime&force_reload=true" method="post">
	<input type="number" value="<?php
	if(empty($_SESSION['media_his_time'])){
		$_SESSION['media_his_time'] = 60000;
	}
	echo $_SESSION["media_his_time"] / 1000; ?>" name="media_his_time">
	<button type="submit"><i class="fa fa-floppy-o"></i> Save</button>
</form>
<h4>Minidlna configuration</h4>
<form action="?ext={{ext_name}}&form=minidlna&force_reload=true" method="post">
	<label>Accessible Install Location</label><br>
	<small>This should be the location of the minidlna.conf and minidlna.pid files usable by the Apache user.</small><br><br>
	<?php 
	
	if(empty($_SESSION['minidlna_dir'])){
		$_SESSION['minidlna_dir'] = '/';
	}
	
	load_controller('ajax_filebrowser', [
		'form' => 'dir',
		'dir' => $_SESSION['minidlna_dir'],
		'root' => ''
	], 'filebrowser'); ?><br><br>
	<label>Minidlna Name</label>
	<small>In most installs this should be &#39;minidlna&#39; but in cases where you have installed minidlna to be run by a specific user this may be &#39;minidlnad&#39;</small><br><br>
	<input type="text" name="minidlna" value="<?php 
		if(empty($_SESSION['minidlna'])){
			$_SESSION['minidlna'] = 'minidlna';
		}
		echo $_SESSION['minidlna'];
	?>">
	<button type="submit"><i class="fa fa-floppy-o"></i> Save</button>
</form>
<h4>The Movie Database</h4>
<form action="?ext={{ext_name}}&form=tmdb&force_reload=true" method="post">
		<label>API Key</label>
		<small>This API key is used to search for data about movies and TV Shows, to get one you must make an account at <a href="https://www.themoviedb.org" rel="noopener nofollow">https://www.themoviedb.org</a>
		<input type="text" name="api_key" value="<?php echo $_SESSION['tmdb_api_key']; ?>">
		<button type="submit"><i class="fa fa-floppy-o"></i> Save</button>
</form>