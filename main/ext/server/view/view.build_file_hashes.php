<header><h1>Build File Hashes</h1></header>
<p>This will search all media items in the database and generate file hashes so that the file can be compared against the hash at a later date.</p>

<p>This enables advanced features like finding moved files</p>
<?php 
echo load_view('ajax_loop_interface', [
    'route' => 'ajax/ajax_build_media_hashes/server'
]);
?>
<br><br><a class="button" href="<?php echo build_slug('', [], 'server'); ?>">Back</a>