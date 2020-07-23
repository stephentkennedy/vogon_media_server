<?php
	//this model generates a salt. The below is just a simple way to get semi-random data, and may very well change. The important part is that a semi-unique salt comes out every time. This model takes no input.
	$temp = 'abcdefghijklmnopqrstuvwxyz';
	$temp .= ucwords($temp);
	$string = '';
	$temp = str_split($temp);
	$keys = array_rand($temp, 26);
	foreach($keys as $key){
		$string .= $temp[$key];
	}
	$salt = date($string);
	$salt = hash('sha1', $salt);
	return $salt;
?>