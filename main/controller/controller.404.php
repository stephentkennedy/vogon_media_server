<?php
	http_response_code(404);
	load_controller('header');
	echo 'Unable to find resource.';
	$footer_data = [
		'footer_nav' => '<a href="'.build_slug('').'">Home</a>'
	];
	load_controller('footer');
?>