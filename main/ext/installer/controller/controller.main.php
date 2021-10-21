<?php
/*
Name: Stephen Kennedy
Date: 12/30/20
Comment: It's long since time we gave this module some love.

We need to add a UI that lets developers choose what extensions get included, as well as what tables. Ideally the extensions should probably identify the tables they require, but that's beyond the scope of what I want to accomplish today.

Additionally, we need to let the developer choose what operating system the install is targeting, as the zip has to be constructed differently for windows and linux. I still feel like that issue is a personal failing in how I'm using the ziparchive class, but work with what we have.
*/
$slug = get_slug_part(1);
switch($slug){
	case 'makearchive':
		error_reporting(E_ALL);
		$version = VER;
		if(empty($_POST['include'])){
			redirect(build_slug('', [], 'installer'));
		}
		if(!empty($_POST['increment'])){
			$level = $_POST['increment_level'];
			$version = load_model('increment_version', ['level' => $level], 'installer');
		}
		if(!empty($_POST['changelog'])){
			load_model('write_changelog', ['changelog' => $_POST['changelog']], 'installer');
		}
		load_controller('header');
		echo '<header><h1>Building Archive</h1></header>';
		$table_data = load_model('parsetables', ['include' => $_POST['include'], 'tables' => $_POST['tables']], 'installer');
		$table_data['structure_file'] = load_model('get_struct', ['tables' => $_POST['tables']], 'installer');
		$filename = load_model('makedbfiles', $table_data, 'installer');
		if(empty($_POST['filename'])){
			$_POST['filename'] = slugify(NAME).'_build_'.date('m_d_y_h');
		}else{
			$_POST['filename'] = slugify($_POST['filename']); //Let's not just shove whatever they decide into the filename
		}
		$archive_data = [
			'filename' => $_POST['filename'],
			'version' => $version
		];
		load_model('makearchive', $archive_data, 'installer');
		echo $filename;
		echo '<br><a href="'.URI.'">Back</a>';
		load_controller('footer');
	break;
	default:
		$table_data = load_model('get_table_data', [], 'installer');
		
		load_controller('header');
		echo load_view('tables', [
			'tables' => $table_data,
		], 'installer');
		load_controller('footer');
		break;
}