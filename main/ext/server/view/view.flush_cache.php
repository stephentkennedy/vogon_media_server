<header><h1>Flush Cache</h1></header>
<?php echo load_view('ajax_loop_interface', [
	'route' => 'ajax/ajax_flush_cache/server'
]); ?>
<br><br><a class="button" href="<?php echo build_slug('', [], 'server'); ?>">Back</a>