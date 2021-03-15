<?php 
$_SESSION['error'] = false;
?>
<header><h1>Update Installer</h1></header>
<?php echo load_view('ajax_loop_interface', [
	'route' => 'ajax/ajax_updater/installer'
]); ?>
<br><br><a class="button" href="<?php echo build_slug('', [], 'server'); ?>">Back</a>