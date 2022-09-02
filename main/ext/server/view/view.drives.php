<table>
	<thead>
		<tr>
			<th>Mount Point</th>
			<th>Status</th>
		</tr>
	</thead>
	<tbody>
<?php foreach($drives as $drive){ ?>
		<tr>
			<td><?php echo $drive['Mounted']; ?></td>
			<td><?php 
				$percent = trim($drive['Use%']);
				$percent = str_replace('%', '', $percent);
				echo load_view('percentage_bar', [
					'percent' => $percent,
					'label' => $drive['Avail'].' free out of '.$drive['Size'].' ('.$drive['Use%'].' Full)'
				], 'server');
			?></td>
		</tr>
<?php } ?>
	</tbody>
</table>
