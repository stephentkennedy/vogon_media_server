<header><h1>Edit Profile</h1></header>
<form method="post">
	<input type="hidden" name="user_key" value="<?php echo $user_key; ?>">
	<label for="user_email">Nickname</label>
	<input type="text" id="user_email" name="user_email" placeholder="example@test.com" value="<?php echo $user_email; ?>">
	<button id="submit" type="submit">Edit</button>
</form>