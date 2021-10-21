<?php
if(!empty($_GET['id']) && is_numeric($_GET['id'])){
	load_model('merge_series', ['id' => $_GET['id']], 'server');
	redirect(build_slug('patch_series', [], 'server'));
}


$series = load_model('get_all_series', [], 'server');
load_controller('header', ['title' => 'Merge Series']);
echo load_view('choose_series_to_patch', ['series' => $series], 'server');
load_controller('footer');
