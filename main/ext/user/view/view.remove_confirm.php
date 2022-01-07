<header><h1>Confirm</h1></header>
<h2>Are you sure you want to remove <?php echo $user_email; ?>?</h2>
<a class="button" href="<?php echo build_slug('remove/'.$user_key.'/confirm', [], 'user'); ?>"><i class="fa fa-check"></i> Yes</a> <a class="button" href="<?php echo build_slug('', [], 'user'); ?>"><i class="fa fa-times"></i> Cancel</a>