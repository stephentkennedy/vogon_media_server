<header><h1>Find Orphan Entries</h1></header>
<p>This will search all audio &amp; video items in your database and mark all items that are no longer linked to their files.</p>
<?php echo load_view('ajax_loop_interface', [
	'route' => 'ajax/ajax_find_orphan_entries/server'
]); ?>
<br><br><a class="button" href="<?php echo build_slug('', [], 'server'); ?>">Back</a>