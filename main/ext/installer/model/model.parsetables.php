<?php
$final_tables = [];
foreach($include as $name => $value){
	if($value == 1){
		$final_tables[$name] = (bool)$_POST['tables'][$name];
	}
}

return load_model('gettables', ['tables' => $final_tables], 'installer');