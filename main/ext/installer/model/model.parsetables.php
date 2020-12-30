<?php
$final_tables = [];
foreach($include as $name){
	$final_tables[$name] = $tables[$name];
}

return load_model('gettables', ['tables' => $final_tables], 'installer');