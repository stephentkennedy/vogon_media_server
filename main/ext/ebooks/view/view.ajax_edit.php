<?php ob_start(); ?>
<form method="post">
    <input type="hidden" name="action" value="edit">
    <input type="hidden" name="id" value="<?php echo $item['data_id']; ?>">
    <label for="data_name">Name</label>
    <input id="data_name" name="data_name" type="text" value="<?php echo $item['data_name']; ?>">
    <label for="data_content">Location</label>
    <input id="data_content" type="text" readonly disabled value="<?php echo $item['data_content']; ?>">
    <label for="series">Series</label>
    <input id="series" name="series" type="text" value="<?php echo $item['parent_data_name']; ?>">
    <label for="author">Author</label>
    <input id="author" name="author" type="text" value="<?php echo $item['author']; ?>">
    <label for="year">Year</label>
    <input id="year" name="year" type="text" value="<?php echo $item['year']; ?>">
    <button type="button" class="submit"><i class="fa fa-floppy-o"></i> Save</button>
</form>
<script type="text/javascript">
    $(document).ready(function(){
        $('#author').autocomplete({
            source: function(request, response){
                $.get('<?php echo build_slug('ajax/ajax_author/ebooks'); ?>', {'search': request.term}).done(function(data){
                    response($.map(data, function(item){
						return{
							label: item.data_meta_content,
							value: item.data_meta_content
						}
					}));
                })
            }
        });
        $('#series').autocomplete({
            source: function(request, response){
                $.get('<?php echo build_slug('ajax/ajax_series/ebooks'); ?>', {'search': request.term}).done(function(data){
                    response($.map(data, function(item){
						return{
							label: item.data_name,
							value: item.data_name
						}
					}));
                })
            }
        });
    });
</script>
<?php
$content = ob_get_clean();
$options = [
	'title' => 'Edit: '.$item['data_name'],
	'width' => '50vw',
	'style' => 'top:100px;left:25vw;'
];
echo load_view('json', [
	'content' => $content,
	'options' => $options
]);