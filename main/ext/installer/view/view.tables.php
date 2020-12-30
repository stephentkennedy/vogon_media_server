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
	<label for="filename">Archive Filename</label>
	<input id="filename" name="filename" value="<?php echo slugify(NAME).'_build_'.date('m_d_y_h'); ?>">
	<button type="submit"><i class="fa fa-cogs"></i> Build</button>
</form>