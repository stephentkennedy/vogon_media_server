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
		if($user['user_key'] == $u['user_key']){
			echo '<td><a href="'.build_slug('edit/'.$u['user_key'], [], 'user').'">[ Edit ]</a></td>';
		}else{
			echo '<td></td>';
		}
		echo '</tr>';
	}
?>
	</tbody>
</table>