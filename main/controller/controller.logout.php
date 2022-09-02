<?php
global $user_model;
$user_model->logout();
redirect(build_slug(''));
die();