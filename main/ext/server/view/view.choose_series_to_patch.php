<header><h1>Choose Series To Patch</h1></header>
<table>
	<thead>
		<tr>
			<th>Name</th>
			<th>Repeated</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($series as $s){
			echo '<tr>';
				echo '<td>';
				echo $s['data_name'];
				echo '</td>';
				echo '<td>';
				echo $s['count'];
				echo '</td>';
				echo '<td>';
				echo '<a class="button" href="'.build_slug('patch_series', ['id' => $s['data_id']], 'server').'">Merge</a>';
				echo '</td>';
			echo '</tr>';
		} ?>
	</tbody>
</table>