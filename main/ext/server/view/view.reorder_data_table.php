<header><h1>Cleanup Data Table</h1></header>
<?php echo load_view('ajax_loop_interface', [
	'route' => 'ajax/ajax_cleanup_data_table/server'
]); ?>
<br><br><a class="button" href="<?php echo build_slug('', [], 'server'); ?>">Back</a>