<?php
	$default_data = [
		'route',
		'var',
		'role'
	];
?>
<header><h1>Export Which Tables?</h1></header>
<form action="<?php echo build_slug('export_tables', [], 'installer'); ?>" method="post">
	<fieldset>
		<legend>Database</legend>
		<table>
			<thead>
				<tr>
					<th>Table name</th>
					<th>Include 
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
					echo '</tr>';
				} ?>
			</tbody>
		</table>
	</fieldset><br><br>
    <label for="filename">Export Name</label>
    <input type="text" name="filename" id="filename" value="table_structure"><br>
	<button type="submit"><i class="fa fa-cogs"></i> Export</button>
</form>