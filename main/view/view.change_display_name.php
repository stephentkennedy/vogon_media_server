<form method="post" action="<?php echo URI; ?>/settings?ext=&form=change_display_name">
	<input type="hidden" name="id" value="<?php echo $id; ?>">
	<input type="text" name="display_name" placeholder="Display Name" value="<?php echo $current_value; ?>">
	<button type="submit">Save</button>
</form>