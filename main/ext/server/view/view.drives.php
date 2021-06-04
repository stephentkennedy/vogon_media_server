<table>
	<thead>
		<tr>
			<th>Mount Point</th>
			<th>Size</th>
			<th>Available</th>
			<th>Full</th>
		</tr>
	</thead>
	<tbody>
<?php foreach($drives as $drive){ ?>
		<tr>
			<td><?php echo $drive['Mounted']; ?></td>
			<td><?php echo $drive['Size']; ?></td>
			<td><?php echo $drive['Avail']; ?></td>
			<td><?php echo $drive['Use%']; ?> <meter value="<?php 
				$percent = trim($drive['Use%']);
				$percent = str_replace('%', '', $percent);
				$percent = (int)$percent / 100;
				echo $percent;
			?>"></meter></td>
		</tr>
<?php } ?>
	</tbody>
</table>