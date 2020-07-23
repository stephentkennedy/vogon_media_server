<header><h1>Choose a profile</h1></header>
<form id="profile_container" method="post">
	<div class="container">
<?php foreach($users as $u){
	echo '<div class="col col-three"><input type="radio" name="user_key" value="'.$u['user_key'].'">'.$u['user_email'].'</div>';
} ?>
	</div>
	<button type="submit">Accept</button>
</div>