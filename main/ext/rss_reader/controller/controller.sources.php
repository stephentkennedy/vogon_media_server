<?php
$action = get_slug_part(2);
if(empty($action)){
    load_controller('header', [
        'title' => 'RSS Sources'
    ]);
    echo load_view('sources', ['sources' => $sources], 'rss_reader');
    load_controller('fooder');
}else{
    switch($action){
        case 'edit':
            if(!empty($_POST['title'])){
                $record_data = [
                    'title' => $_POST['title'],
                    'url' => $_POST['url'],
                ];
                if(!empty($_POST['id'])){
                $item_check = load_model('get_source', ['id' => $_POST['id']], 'rss_reader');
                    if(empty($item_check)){
                        //Can't edit other people's sources.
                        redirect('/');
                    }else{
                        $record_data['id'] = $_POST['id'];
                    }
                }
                $id = load_model('edit_source', $record_data, 'rss_reader');
                $redirect = build_slug('sources/edit/'.$id, [], 'rss_reader');
                redirect($redirect);
            }
            $item = [
                'data_name' => ''
            ];
            $id = get_slug_part(3);
            if(!empty($id)){
                $item_check = load_model('get_source', ['id' => $id], 'rss_reader');
                if(!empty($item_check)){
                    $item = $item_check;
                }
            }
            load_controller('header', [
                'title' => 'Edit Source:'.$item['data_name']
            ]);
            echo load_view('edit_source', ['item' => $item], 'rss_reader');
            load_controller('footer');
            break;
        case 'remove':
            $id = get_slug_part(3);
            if(!empty($id)){
                load_model('remove_source', ['id' => $id], 'rss_reader');
            }
            redirect(build_slug('sources', [], 'rss_reader'));
            break;
    }
}