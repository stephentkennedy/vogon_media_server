<h2>Extensions</h2>
<table>
    <?php 
        foreach($exts as $ext){
            echo '<tr>';
                echo '<td>';
                echo $ext;
                echo '</td>';
                echo '<td>';
                echo '<a href="'.build_slug('', [
                    'action' => 'install_ext',
                    'ext' => $ext
                ], 'settings').'"><i class="fa fa-cog"></i> Install</a>';
                echo '</td>';
            echo '</tr>';
        }
    ?>
</table>