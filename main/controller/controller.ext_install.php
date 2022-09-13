<?php 
load_class('db_handler');
$route_table = new db_handler('route');

$search = [
    'ext_primary' => 1,
    'ext' => $extension
];

$check = $route_table->getRecord($search);

if(empty($check)){
    $defaults = load_model('get_install_vars', [], $extension);
    if(!empty($defaults)){

        //Allow us to define a default route upon installation
        if(!empty($defaults['route'])){
            $search['slug'] = $defaults['route'];
            $search['in_h_nav'] = 1;

            $double_check = $route_table->getRecord(['route_slug' => $defaults['route']]);
            if(!empty($double_check)){
                $search['slug'] = $defaults['route'].'_'.$extension;
            }

            if(!empty($defaults['display'])){
                $search['nav_display'] = $defaults['display'];
            }

            if(empty($defaults['controller'])){
                $search['controller'] = 'main';
            }else{
                $search['controller'] = $defaults['controller'];
            }

            $route_table->addRecord($search);
            load_model('rebuild_nav', ['type' => 'head']);
            redirect(build_slug('', [], 'settings'));
        }

        //Allow us to install non-standard tables if we have them.
        if(
            !empty($defaults['tables'])
            && file_exists(ROOT . DIRECTORY_SEPARATOR . 'main' . DIRECTORY_SEPARATOR . 'ext' . DIRECTORY_SEPARATOR . 'installer')
        ){
            $settings = parse_ini_file(ROOT . DIRECTORY_SEPARATOR . 'main'. DIRECTORY_SEPARATOR . 'config.ini', true);
            $db_name = $settings['database']['name'];
            $sql = 'SELECT table_name FROM information_schema.tables WHERE table_schema = :name';
            $params = [
                ':name' => $db_name
            ];
            
            $table_return = $db->t_query($sql, $params)->fetchAll();
            $tables = [];
            foreach($table_return as $t){
                $tables[$t['table_name']] = false;
            }
            
            $model_data = [
                'cur_struct' => load_model('get_struct', ['tables' => $tables], 'installer'),
                'new_struct' => $defaults['tables']
            ];
            
            $db_diff = load_model('db_diff', $model_data, 'installer');
            
            load_model('install_new_tables', ['new' => $db_diff['new'], 'db_tables' => json_decode(file_get_contents($db_tables), true)], 'installer');
            
            load_model('update_tables', ['change' => $db_diff['change']], 'installer');
        }
    }
}