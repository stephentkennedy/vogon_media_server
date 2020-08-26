<h3>Video Media Settings</h3>
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
	<label>Minidlna Daemon Name</label>
	<small>This is run as a shell command, with -f and -P flags, so don't put anything here that could ruin your server</small><br><br>
	<input type="text" name="minidlna" value="<?php 
		if(empty($_SESSION['minidlna'])){
			$_SESSION['minidlna'] = 'minidlnad';
		}
		echo $_SESSION['minidlna'];
	?>">
	<button type="submit"><i class="fa fa-floppy-o"></i> Save</button>
</form>