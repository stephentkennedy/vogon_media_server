<?php

if(!empty($_POST['enable_bool'])){
	put_var('bg_vid_bool', true, 'string', true);
}else{
	put_var('bg_vid_bool', false, 'string', true);
}
put_var('bg_vid', $_POST['file'], 'string', true);