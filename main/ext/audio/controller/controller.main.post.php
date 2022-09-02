<?php
if(isset($_REQUEST['action'])){
	switch($_REQUEST['action']){
		case 'edit': //Should only appear as a POST
			load_model('edit_item', $_POST, 'audio');
			break;
		case 'ajax_edit': //Should only appear as a POST
			load_model('edit_item', $_POST, 'audio');
			die('saved');
			break;
		case 'enable_history': //Should only appear as a GET
			$id = get_slug_part(2);
			load_model('enable_history', ['id' => $id], 'audio');
			redirect(build_slug('album/'.$id, [], 'audio'));
			break;
		case 'disable_history': //Should only appear as a GET
			$id = get_slug_part(2);
			load_model('enable_history', ['id' => $id], 'audio');
			redirect(build_slug('album/'.$id, [], 'audio'));
			break;
	}
}