<?php
/*
Name: Steph Kennedy
Date: 12/30/2019
Comment: This model should search a string for all instances of a regular expression, return the string minus those instances, and return the instances in an array of matches.
*/
preg_match_all($filter, $string, $matches);
if(empty($level)){
	$level = 0;
}
$r_matches = []; //Make sure we're just dealing with an empty array.
$r_matches = $matches[$level];
$r_string = preg_replace($filter, '', $string);
return[
	'string' => $r_string,
	'matches' => $r_matches
];