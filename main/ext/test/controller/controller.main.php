<?php
error_reporting(E_ALL);
load_controller('header');
echo load_view('test', [], 'test');
load_controller('footer');