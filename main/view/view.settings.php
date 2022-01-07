<header><h1>Settings</h1></header>
<h2>Framework Settings</h2>
<h3>Routes</h3>
<p>Routes connect individual controllers to specific URI endpoints. A controller can then load other controllers based on its programmed logic, so a route&#39;s controller may not be controlled solely by the extension it is associated with.</p>
<h4>New Route</h4>
<form action="?ext=&form=route&force_reload=true" method="post">
	<label>Slug</label>
	<input type="text" name="slug" placeholder="Slug..." pattern="/[a-zA-Z\_\-]+/">
	<label>Controller</label>
	<input type="text" name="controller" placeholder="Controller...">
	<label>Extension</label>
	<select name="ext">
		<option value="">None</option>
	<?php
		foreach($_SESSION['loaded_extensions'] as $ext){
			$friendly = ucwords(str_replace('_', ' ', $ext));
			echo '<option value="'.$ext.'">'.$friendly.'</option>';
		}
	?>
	</select>
	<button type="submit"><i class="fa fa-plus"></i> Add</button>
</form>
<h4>Current Routes</h4>
<div>
<?php echo load_view('search_form'); ?>
<table class="routes">
	<thead>
		<tr>
			<th>Slug</th>
			<th>Controller</th>
			<th>Extension</th>
			<th>Display</th>
			<th>In Header</th>
			<th>In Footer</th>
			<th>Primary</th>
			<th>Actions</th>
		</tr>
	</thead>
	<?php
		foreach($routes as $r){
			echo '<tr>
				<td>/'.$r['route_slug'].'</td>
				<td>'.$r['route_controller'].'</td>
				<td>'.$r['route_ext'].'</td>
				<td>'.$r['nav_display'].' <a class="open-popup right" data-title="Edit display name" data-src="'. build_slug('/ajax/settings?action=form&form=change_display_name&id='.$r['route_id']) . '">[Edit]</a></td>
				<td>';
				if($r['in_h_nav']){
					echo 'Yes';
				}else{
					echo 'No';
				}
			echo '</td>
				<td>';
				if($r['in_f_nav']){
					echo 'Yes';
				}else{
					echo 'No';
				}
			echo '</td>
				<td>';
				if($r['ext_primary']){
					echo 'Yes';
				}else{
					echo 'No';
				}
			echo '</td>
				<td>
					<a class="action toggle" href="?ext=&form=route_toggle_h&route_id='.$r['route_id'].'&force_reload=true">[Toggle Header]</a>
					<a class="action toggle" href="?ext=&form=route_toggle_f&route_id='.$r['route_id'].'&force_reload=true">[Toggle Footer]</a>
					<a class="action toggle" href="?ext=&form=route_toggle_m&route_id='.$r['route_id'].'&force_reload=true">[Toggle Main]</a>
					<a class="action remove" href="?ext=&form=route_remove&route_id='.$r['route_id'].'&force_reload=true">[Remove Route]</a>
				</td></tr>';
		}
	?>
</table>
<?php echo $_SESSION['pagination']; ?>
<br><a class="button" href="?ext=&form=rebuild_nav&force_reload=true"><i class="fa fa-refresh"></i> Rebuild Navigation</a>
</div>
<h3>Error Reporting</h3>
<form action="?ext=&form=error_reporting&force_reload=true" method="post">
	<label>Level</label>
	<select name="level">
		<?php 
			$levels = [
				E_ALL => 'All errors and notices',
				E_STRICT => 'Errors which may interfere with future PHP versions',
				E_USER_ERROR => 'Show only code defined errors',
				E_ERROR => 'Show only fatal errors',
				E_WARNING => 'Show non-fatal errors',
				0 => 'Show nothing'
			];
			foreach($levels as $val => $friendly){
				$selected = '';
				if(isset($_SESSION['error_reporting']) && $_SESSION['error_reporting'] == $val){
					$selected = ' selected';
				}
				echo '<option value="'.$val.'"'.$selected.'>'.$friendly.'</option>';
			}
		?>	
	</select>
	<button type="submit"><i class="fa fa-floppy-o"></i> Save</button>
</form>
<h3>Active Theme</h3>
<form action="?ext=&form=theme&force_reload=true" method="post">
	<select name="theme">
		<?php
			foreach($themes as $t){
				if($t == 'layout.css'){
					//Skip our layout rules, as they are alway loaded.
					continue;
				}
				$selected = '';
				if($t == $active_theme){
					$selected = ' selected';
				}
				echo '<option value="'.$t.'"'.$selected.'>'.ucwords(str_replace(['_', '.css'], [' ', ''], $t)).'</option>';
			}
		?>
	</select>
	<button type="submit"><i class="fa fa-floppy-o"></i> Save</button>
</form>
<h3>Background</h3>
<small>This media server supports and image or looping video playback in the background to add a little extra visual polish. It is recommended that the video chosen here be very small, like the default sample video provided.</small><br><br>
<form action="?ext=&form=background_video" method="post">
	<input id="video-background-bool" type="checkbox" value="1" name="enable_bool" <?php if(!isset($_SESSION['bg_vid_bool']) || $_SESSION['bg_vid_bool'] == 1){
		echo 'checked';
	} ?>> <label class="inline">Enable</label>
	<fieldset class="variable-section <?php if(!isset($_SESSION['bg_vid_bool']) || $_SESSION['bg_vid_bool'] == 1){ echo 'active'; } ?>" data-id="video-background-bool" data-value="1">
	<?php 
	if(empty($_SESSION['bg_vid'])){
		$_SESSION['bg_vid'] = ROOT . DIRECTORY_SEPARATOR . 'upload' . DIRECTORY_SEPARATOR . 'color_loop_web_small.mp4';
	}
	load_controller('ajax_filebrowser', [
		'form' => 'file',
		'b_file' => $_SESSION['bg_vid'],
		'root' => ROOT
	], 'filebrowser'); ?>
	</fieldset>
	<button type="submit"><i class="fa fa-floppy-o"></i> Save</button>
</form>
<h2>Extension Settings</h2>