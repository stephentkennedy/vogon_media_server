<?php
$old_type = 'series';
$new_type = 'ebook_series';
load_model('change_type', [
    'old_type' => $old_type,
    'new_type' => $new_type
], 'ebooks');