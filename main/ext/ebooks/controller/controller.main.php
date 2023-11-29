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
	//case 'series':
	//	load_controller('series', [], 'ebooks');
	//	break;
	case 'serve':
		load_controller('serve_file', [], 'ebooks');
		break;
	case 'view':
		load_controller('comic_book_reader', [], 'ebooks');
		break;
	case 'series_editor':
		load_controller('header');
		echo load_view('series_editor', [], 'ebooks');
		load_controller('footer');
		break;
	default:
		if(isset($_GET['resume_search'])){
			echo load_view('ls_redirect', [], 'ebooks');
			die();
		}

		load_controller('header', ['title' => 'E-Book Library']);
		echo load_view('main', [], 'ebooks');
		load_controller('footer');
		break;
}
