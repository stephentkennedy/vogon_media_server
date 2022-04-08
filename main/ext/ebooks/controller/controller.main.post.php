<?php 
if(isset($_REQUEST['action'])){
    switch($_REQUEST['action']){
        case 'edit':
        case 'ajax_edit':
            load_model('edit_item', $_POST, 'ebooks');
            break;
    }
}