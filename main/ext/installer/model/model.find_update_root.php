<?php

$items = array_diff(scandir($start_dir), ['.', '..']);
$found = false;
foreach($items as $item){
	if(is_dir($start_dir . DIRECTORY_SEPARATOR . $item) && $item == 'main'){
		$temp = $start_dir . DIRECTORY_SEPARATOR . $item . DIRECTORY_SEPARATOR;
		$check1 = file_exists($temp.'bootstrap.php');
		$check2 = file_exists($temp.'router.php');
		$check3 = file_exists($temp.'functions.php');
		if($check1 == true && $check2 == true && $check3 == true){
			return $start_dir;
		}
	}else if(is_dir($start_dir . DIRECTORY_SEPARATOR . $item)){
		$found = load_model('find_update_root', ['start_dir' => $start_dir . DIRECTORY_SEPARATOR . $item], 'installer');
		if($found != false){
			return $found;
		}
	}
}
return $found;