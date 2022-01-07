<header><h1>Profiles <a href="<?php echo build_slug('new', [], 'user'); ?>">[ New ]</a></h1></header>
<?php echo load_view('search_form'); ?>
<table>
	<thead>
		<tr><th>Profile</th><th></th></tr>
	</thead>
	<tbody>
<?php
	foreach($users as $u){
		echo '<tr>';
		echo '<td>'.$u['user_email'].'</td>';
		global $user;
		echo '<td><a class="button" href="'.build_slug('edit/'.$u['user_key'], [], 'user').'"><i class="fa fa-pencil"></i> Edit</a> <a class="button" href="'.build_slug('remove/'.$u['user_key'], [], 'user').'"><i class="fa fa-times"></i> Remove</td>';
		echo '</tr>';
	}
?>
	</tbody>
</table>