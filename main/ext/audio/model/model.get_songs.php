<?php

$clerk = new clerk;
$song_data = [];
foreach($songs as $s){
	$song_data[] = $clerk->getRecord($s, true);
}
return [
	'songs' => $song_data
];