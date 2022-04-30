<?php
$sql = 'SELECT data_id, count(data_id) as `count`, data_name FROM data WHERE data_type = "series" GROUP BY data_name ORDER BY data_id';
$series = $db->query($sql, [])->fetchAll();

foreach($series as $s){
    if($s['count'] == 1){
        continue;
    }
    $sql = 'SELECT data_id FROM data WHERE data_type = "series" AND data_name = :series ORDER BY data_id';
    $params = [
        ':series' => $s['data_name']
    ];
    $results = $db->query($sql, $params)->fetchAll();
    if(!empty($results) && count($results) > 1){
        $root = false;
        $key = [];
        foreach($results as $i => $r){
            if($i == 0){
                $root = $r['data_id'];
            }else{
                $key[] = $r['data_id'];
            }
        }

        $sql = 'UPDATE data SET data_parent = :root WHERE data_parent IN('.implode(',', $key).')';
        $params = [
            ':root' => $root
        ];
        $db->query($sql, $params);

        $sql = 'DELETE FROM data WHERE data_id IN('.implode(',', $key).')';

        $db->query($sql, []);
    }

}