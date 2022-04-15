<?php 
$action = get_slug_part(1);
switch($action){
	case 'edit':
		$id = get_slug_part(2);
		$item = load_model('get_item_by_id', ['id' => $id], 'ebooks');
		if(empty($_GET['format'])){
			$_GET['format'] = '';
		}
		switch($_GET['format']){
			case 'ajax_form':
				echo load_view('ajax_edit', ['item' => $item], 'ebooks');
				break;
			default:
				break;
		}
		break;
	case 'view':
		load_controller('comic_book_reader', [], 'ebooks');
		break;
	default:
		load_controller('header', ['title' => 'E-Book Library']);
		echo load_view('main', [], 'ebooks');
		load_controller('footer');
		break;
}
