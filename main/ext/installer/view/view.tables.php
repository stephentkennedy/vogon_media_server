<?php
	$default_data = [
		'route',
		'var',
		'role'
	];
?>
<header><h1>Build your Vogon</h1></header>
<form action="<?php echo build_slug('makearchive', [], 'installer'); ?>" method="post">
	<fieldset>
		<legend>Database</legend>
		<table>
			<thead>
				<tr>
					<th>Table name</th>
					<th>Include 
					<th>Data Options</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($tables as $t){
				//Tabbed to help the logic flow.
					echo '<tr>';
						echo '<td>'.$t.'</td>';
						echo '<td>';
						echo '<select name="include['.$t.']">
							<option value="1">Yes</option>
							<option value="0">No</option>
						</select>';
						echo '</td>';
						echo '<td>';
							echo '<select name="tables['.$t.']">';
							echo '<option value="1"';
							if(in_array($t, $default_data)){
								echo ' selected';
							}
							echo '>Data &amp; Structure</option>';
							echo '<option value="0"';
							if(!in_array($t, $default_data)){
								echo ' selected';
							}
							echo '>Structure Only</option>';
							echo '</select>';
						echo '</td>';
					echo '</tr>';
				} ?>
			</tbody>
		</table>
	</fieldset><br><br>
	<label for="increment">Increment Version?</label>
	<input type="checkbox" value="1" id="increment" name="increment"> <label class="inline" for="increment">Yes</label>
	<fieldset class="variable-section" data-id="increment" data-value="1">
		<label for="increment_level">Increment Type</label>
		<select id="increment_level" name="increment_level">
			<option value="lifecycle">Lifecycle</option>
			<option value="major">Major</option>
			<option value="minor">Minor</option>
			<option value="hotfix" selected>Hotfix</option>
		</select>
		<label for="increment_preview">Resulting Version</label>
		<input type="text" readonly id="increment_preview" value="<?php 
			load_class('vParse');
			$v = new vParse;
			$new = $v->increment('hotfix');
			echo $new;
		?>">
		<label for="changelog">Change Log</label>
		<textarea id="changelog" name="changelog"></textarea>
		<script type="text/javascript">
			$(document).ready(function(){
				$('#increment_level').change(function(){
					var data = {
						'level': $('#increment_level').val()
					};
					$.get(<?php echo "'".build_slug('ajax/ajax_increment/installer')."'"; ?>, data, function(returned){
						$('#increment_preview').val(returned);
					});
				});
			});
		</script>
	</fieldset><br>
	<label for="filename">Archive Filename</label>
	<input id="filename" name="filename" value="<?php echo slugify(NAME).'_build_'.date('m_d_y_h'); ?>">
	<button type="submit"><i class="fa fa-cogs"></i> Build</button>
</form>