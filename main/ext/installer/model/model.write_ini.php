<?php
$string = '';
foreach($ini as $label => $value){
	if(is_array($value)){
		$string .= '['.$label.']' . PHP_EOL;
		foreach($value as $sub_label => $sub_value){
			$string .= $sub_label . ' = ';
			if(is_numeric($sub_value)){
				$string .= $sub_value;
			}else{
				$string .= '"'.str_replace('"', '\"', $sub_value).'"';
			}
			$string .= PHP_EOL;
		}
	}else{
		$string .= $label . ' = ';
		if(is_numeric($value)){
			$string .= $value;
		}else{
			$string .= '"'.str_replace('"', '\"', $value).'"';
		}
		$string .= PHP_EOL;
	}
}

return file_put_contents($filename, $string);