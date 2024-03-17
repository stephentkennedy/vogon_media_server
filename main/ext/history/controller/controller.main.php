<?php

load_controller('header', ['title' => 'User History']);

echo load_view('main', [], 'history');

load_controller('footer');