<header><h1>Choose a profile</h1></header>
<form id="profile_container" method="post">
	<div class="container row wide">
<?php foreach($users as $u){
	echo '<div class="col col-three"><input id="user_'.$u['user_key'].'" type="radio" name="user_key" value="'.$u['user_key'].'"> <label for="user_'.$u['user_key'].'">'.$u['user_email'].'</label></div>';
} ?>
	</div>
	<button type="submit">Accept</button>
</div>