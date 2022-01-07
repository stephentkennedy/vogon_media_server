<?php
die('Dangerous Route, disabled on line 2. Remove if you know what you\'re doing.');

$c = new clerk;

$audio = $c->getRecords(['type'=>'audio']);
foreach($audio as $a){
	$f = $a['data_content'];
	if(!file_exists($f)){
		echo $f.'<br>';
		$c->removeRecord($a['data_id']);
	}
}