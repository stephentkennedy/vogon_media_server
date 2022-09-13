
<header><h1>Edit Source: <?php if(!empty($item['data_name'])){ echo $item['data_name']; } ?> <a class="button" href="<?php echo build_slug('sources', [], 'rss_reader'); ?>">Back</a></h1></header>
<form method="post">
    <?php 
        if(!empty($item['data_id'])){
            echo '<input type="hidden" name="id" value="'.$item['data_id'].'">';
        }
    ?>
    <label for="title">Title</label>
    <input type="text" name="title" id="title" value="<?php if(!empty($item['data_name'])){ echo $item['data_name']; } ?>">
    <label for="url">URL</label>
    <input type="text" name="url" id="url" value="<?php if(!empty($item['data_content'])){ echo $item['data_content']; } ?>">
    <button type="submit" class="button"><i class="fa fa-floppy-o"></i> Save</button>
</form>