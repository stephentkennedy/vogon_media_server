<h2>EBook Settings</h2>
<h4>Resize CBZ Images</h4>
<p class="small">Some low memory devices may not be able to render high quality images, especially at higher resolutions. The server can be instructed to resize those images in memory to the device resolution before serving them.</p>
<form action="?ext={{ext_name}}&form=resizecbz&force_reload=true" method="post">
    <label for="resize_images">Resize images before serving?</label>
    <select id="resize_images" name="resize_images"><?php
        $options = [
            'false' => 'No',
            'true' => 'Yes'
        ];
        if(isset($_SESSION['resize_cbz']) && $_SESSION['resize_cbz'] == 'true'){
            $selected = 'true';
        }else{
            $selected = 'false';
        }
        $string = '';
        foreach($options as $value => $label){
            $string .= '<option value="'.$value.'"';
            if($selected == $value){
                $string .= ' selected';
            }
            $string .= '>'. $label.'</option>';
        }
        echo $string;
    
    ?></select>
    <button type="submit"><i class="fa fa-floppy-o"></i> Save</button>
</form>