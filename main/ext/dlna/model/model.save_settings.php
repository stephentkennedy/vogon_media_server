<?php

if(!empty($_POST['friendly_name'])){
    put_var('dlna_friendly_name', $_POST['friendly_name'], 'string', true);
}

if(!empty($_POST['bound_ip'])){
    put_var('dlna_bound_ip', $_POST['bound_ip'], 'string', true);
}

put_var('dlna_xsendfile', !empty($_POST['xsendfile']) ? '1' : '0', 'string', true);