<h3>Audio Library Settings</h3>
<h4>Default Visualizer</h4>
<form action="?ext={{ext_name}}&form=audioviz&force_reload=true" method="post">
	<select name="viz">
		<?php
			$visualizers = [
				'cleanCircle' => 'Circle',
				'bars' => 'Bars',
				'spectro' => 'Spectrograph'
			];
			foreach($visualizers as $var => $friendly){
				$selected = '';
				if(isset($_SESSION['def_visual']) && $var == $_SESSION['def_visual']){
					$selected = ' selected';
				}
				echo '<option value="'.$var.'"'.$selected.'>'.$friendly.'</option>';
			}
		?>
	</select>
	<button type="submit"><i class="fa fa-floppy-o"></i> Save</button>
</form>
<h4>History Sync (In Seconds)</h4>
<small>The audio player also syncs with the server when the audio is paused</small><br><br>
<form action="?ext={{ext_name}}&form=audiohistime&force_reload=true" method="post">
	<input type="number" value="<?php
	if(empty($_SESSION['audio_his_time'])){
		$_SESSION['audio_his_time'] = 10000;
	}
	echo $_SESSION["audio_his_time"] / 1000; ?>" name="audio_his_time" min="0">
	<button type="submit"><i class="fa fa-floppy-o"></i> Save</button>
</form>