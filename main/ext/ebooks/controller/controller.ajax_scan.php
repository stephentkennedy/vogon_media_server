<?php
echo load_view('ajax_scan', [
	'route' => 'ajax/ajax_ebook_import/ebooks?dir=' . urlencode($dir)
], 'ebooks');
