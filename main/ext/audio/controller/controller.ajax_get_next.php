<?php


$return = load_model('get_next_member', ['id' => (int)$_GET['id']], 'audio');

//Doing this a lot so I created a core view for it.
echo load_view('json', $return);