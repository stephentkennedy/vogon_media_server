<?php

$parsed_data = load_model('search_parse', ['query' => $search_query]);
$query_data = load_model('search_build_query', [
	'query' => $parsed_data['parsed'],
	'tables' => $tables,
	'links' => $links
]);

if(empty($_REQUEST['rpp'])){
	$_REQUEST['rpp'] = 25;
}
if(empty($_REQUEST['page'])){
	$_REQUEST['page'] = 1;
}
if(empty($_REQUEST['search'])){
	$_REQUEST['search'] = '';
}
$_REQUEST['page'] = (int) $_REQUEST['page'];
$_REQUEST['rpp'] = (int) $_REQUEST['rpp'];

/*
Name: Stephen Kennedy
Date: 2/7/2020
Comment: This is rife for SQL injection. You need to fix this before you upload it to a live server.
*/
if(!empty($_REQUEST['orderby'])){
	$query_data['sql'] .= 'ORDER BY '.str_replace([';'], '', $_REQUEST['orderby']);
}

$offset = $_REQUEST['page'] - 1;
$offset = $offset * $_REQUEST['rpp'];
$query_data['sql'] .= ' LIMIT '.$offset.', '.$_REQUEST['rpp'];

$query = $db->query($query_data['sql'], $query_data['params']);
if($query == false){
	return false;
}

$pages = ceil($query_data['total'] / $_REQUEST['rpp']);
$_SESSION['pagination'] = '';
if($pages > 1){
	for($i = 1; $i <= $pages; $i++){
		if($i == 1 && $i < ($_REQUEST['page'] - 2)){
			$_SESSION['pagination'] .= '<a class="pagination" href="?action=search&search='.urlencode($_REQUEST['search']).'&rpp='.$_REQUEST['rpp'].'&page='.$i.'&orderby='.urlencode($_REQUEST['orderby']).'">[ FIRST ]</a>';
		}
		if($i >= ($_REQUEST['page'] - 2) && $i <= ($_REQUEST['page'] + 2) && $i != $_REQUEST['page']){
			$_SESSION['pagination'] .= '<a class="pagination" href="?action=search&search='.urlencode($_REQUEST['search']).'&rpp='.$_REQUEST['rpp'].'&page='.$i.'&orderby='.urlencode($_REQUEST['orderby']).'">['.$i.']</a>';
		}
		if($i == $_REQUEST['page']){
			$_SESSION['pagination'] .= '<a class="pagination active" href="?action=search&search='.urlencode($_REQUEST['search']).'&rpp='.$_REQUEST['rpp'].'&page='.$i.'&orderby='.urlencode($_REQUEST['orderby']).'">['.$i.']</a>';
		}
		if($i == $pages && $i > ($_REQUEST['page'] + 2)){
			$_SESSION['pagination'] .= '<a class="pagination" href="?action=search&search='.urlencode($_REQUEST['search']).'&rpp='.$_REQUEST['rpp'].'&page='.$i.'&orderby='.urlencode($_REQUEST['orderby']).'">[ END ]</a>';
		}
	}
}
$_SESSION['pagination'] .= ' '.$query_data['total']. ' results';
return $query->fetchAll();