<header><h1>Cleanup Data Meta Table</h1></header>
<?php echo load_view('ajax_loop_interface', [
	'route' => 'ajax/ajax_cleanup_data_meta_table/server'
]); ?>
<br><br><a class="button" href="<?php echo build_slug('', [], 'server'); ?>">Back</a>