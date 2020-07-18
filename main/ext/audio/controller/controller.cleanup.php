<?php
$c = new clerk;

$audio = $c->getRecords(['type'=>'audio']);
foreach($audio as $a){
	$c->removeRecord($a['data_id']);
}