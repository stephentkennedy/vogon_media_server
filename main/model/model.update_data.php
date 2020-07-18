<?php
$record = [];
if(!empty($name)){
	$record['name'] = $name;
}
if(!empty($slug)){
	$record['slug'] = $slug;
}
if(!empty($content)){
	$record['content'] = $content;
}
if(!empty($parent)){
	$record['parent'] = $parent;
}
if(!empty($status)){
	$record['status'] = $status;
}
if(!empty($user_key)){
	$record['user'] = $user_key;
}
$clerk = new clerk;
return $clerk->updateRecord($record, $id);