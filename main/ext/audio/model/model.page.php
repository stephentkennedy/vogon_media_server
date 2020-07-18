<?php

$pages = ceil($count / $ipp);

$return = [
	'first' => 1,
	'last' => $pages,
	'cur' => $page
];

if($page > 1){
	$return['prev'] = $page - 1;
}
if($page > 2){
	$return['prev_prev'] = $page - 2;
}
if($page < $pages){
	$return['next'] = $page + 1; 
}
if($page < $pages - 1){
	$return['next_next'] = $page + 2;
}

return $return;