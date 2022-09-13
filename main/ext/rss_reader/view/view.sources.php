<header><h1>RSS Sources <a class="button" title="Add Source" href="<?php echo build_slug('sources/edit', [], 'rss_reader'); ?>"><i class="fa fa-plus"></i></a> <a class="button" href="<?= build_slug('', [], 'rss_reader'); ?>" title="Back to feed"><i class="fa fa-arrow-left"></i></a></h1></header>
<?php 
    if(empty($sources)){
        ?> 
<h2>You have no current sources for your RSS Feed</h2>
<a class="button" title="Add Source" href="<?php echo build_slug('sources/edit', [], 'rss_reader'); ?>"><i class="fa fa-plus"></i> Add One</a>
        <?php
    }else{
        ?>
<table>
    <thead>
        <tr>
            <th>
            Title
            </th>
            <th>
            URL
            </th>
            <th>
            </th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach($sources as $source){
            echo '<tr>';
                echo '<td>';
                echo $source['data_name'];
                echo '</td>';
                echo '<td>';
                echo $source['data_content'];
                echo '</td>';
                echo '<td>';
                echo '<a class="button" href="'.build_slug('sources/edit/'.$source['data_id'], [], 'rss_reader').'"><i class="fa fa-pencil"></i> Edit</a>';
                echo ' <a class="button" href="'.build_slug('sources/remove/'.$source['data_id'], [], 'rss_reader').'"><i class="fa fa-times"></i> Remove</a>';
                echo '</td>';
            echo '</tr>';
        }
        ?>
    </tbody>
</table>
        <?php
    }
?>