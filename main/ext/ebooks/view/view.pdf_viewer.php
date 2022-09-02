<?php 
    $path = str_replace(ROOT, '', $item['data_content']);
    $path = explode('/', $path);
    foreach($path as $key => $field){
        $path[$key] = str_replace('#', urlencode('#'), $field);
    }
    $path = implode('/', $path);
?><iframe src="<?php echo $path; ?>" style="width: 100%; height: 100vh;"></iframe>