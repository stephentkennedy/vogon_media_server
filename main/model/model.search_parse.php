<?php
/*
Name: Stephen Kennedy
Date: 12/2/19
Comment: This model is supposed to take search input from the user, parse it for special symbols, and modify our search logic accordingly

To this end, we should do our best not to modify our original
$query
variable, in case we need to return to it.

We also need to define a logical order of operations
*/
$parsed_query = $query;

$or_pattern = '/\|\|/';
$and_pattern = '/(\&\&)/';
$not_pattern = '/\-([a-zA-Z\_0-9]+)/';
$key_pattern = '/([a-zA-Z\_0-9]+)\:(.+)[.|,]/U';
$quote_pattern = '/\"(.+)\"/U';

$chunked_query = preg_split($or_pattern, $parsed_query);
foreach($chunked_query as $key => $chunk){
	$chunk = preg_split($and_pattern, $chunk);
	foreach($chunk as $k => $c){
		$not_data = load_model('filter_pattern', [
			'string' => $c,
			'filter' => $not_pattern,
			'level' => 1
		]);
		$c = $not_data['string'];
		$key_data = load_model('filter_pattern', [
			'string' => $c,
			'filter' => $key_pattern,
			'level' => 0
		]);
		$c = $key_data['string'];
		$quote_data = load_model('filter_pattern', [
			'string' => $c,
			'filter' => $quote_pattern,
			'level' => 1
		]);
		$c = $quote_data['string'];
		$c = preg_replace('/[^a-zA-Z0-9]/', ' ', $c); //Now that we've parsed, we are going to remove all alpha numeric characters to help us build a safe query
		$c = load_model('filter_stopwords', ['string' => $c]);
		$c = preg_replace('/\s{1,}/', ' ', $c); //Collapse the whitespace;
		$members = explode(' ', $c);
		if(count($quote_data['matches']) > 0){
			foreach($quote_data['matches'] as $match){
				$members[] = str_replace('"', '', $match); //Append our exact search matches;
			}
		}
		foreach($members as $k => $m){
			if(empty(trim($m))){
				unset($members[$k]);
			}
		}
		sort($members);
		$chunk[$k] = [
			'members' => $members,
			'not' => $not_data['matches'],
			'keys' => $key_data['matches']
		];
	}
	if(gettype($chunk[0]) != 'array'){
		unset($chunk[0]);
		sort($chunk);
	}
	$chunked_query[$key] = $chunk;
}
return [
	'parsed' => $chunked_query,
	'original' => $query
];