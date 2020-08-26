<?php
 if(!empty($form) && $form == 'dir'){
?>
<label>Selected Directory:</label>
<input id="form-dir-<?php echo $_SESSION['active_filebrowsers'];?>" type="text" name="dir" value="<?php echo $dir; ?>" readonly>
<?php }else if(!empty($form) && $form == 'file'){
?><label>Selected File:</label>
<input id="form-file-<?php echo $_SESSION['active_filebrowsers'];?>" type="text" name="file" value="<?php if(!empty($b_file)){ echo $b_file; } ?>" readonly><?php
} 
	echo $preload;
?><script type="text/javascript">
	var fbrowser<?php echo $_SESSION['active_filebrowsers'];?> = {
		form: <?php if(!empty($form) && $form == true){ echo 'true'; }else{ echo 'false'; } ?>,
		get: function(dir){
			$('.dir-container-<?php echo $_SESSION['active_filebrowsers'];?>').html('<i class="fa fa-spin fa-cog"></i>');
			$.get('/ajax/ajax_main/filebrowser', {'dir': dir}).done(fbrowser<?php echo $_SESSION['active_filebrowsers'];?>.load);
		},
		load: function(data){
			$('.dir-container-<?php echo $_SESSION['active_filebrowsers'];?>').html(data.content);
			if(fbrowser<?php echo $_SESSION['active_filebrowsers'];?>.form == true){
				$('#form-dir-<?php echo $_SESSION['active_filebrowsers'];?>').val(data.dir);
			}
			fbrowser<?php echo $_SESSION['active_filebrowsers'];?>.bind();
		},
		click: function(e){
			var dom = e.target;
			var isDir = $(dom).hasClass('dir');
			if(isDir){
				var dir = dom.getAttribute('data-loc');
				console.log(dir);
				fbrowser<?php echo $_SESSION['active_filebrowsers'];?>.get(dir);
			}else if(fbrowser<?php echo $_SESSION['active_filebrowsers'];?>.form == true){
				var file = dom.getAttribute('data-loc');
				$('#form-file-<?php echo $_SESSION['active_filebrowsers'];?>').val(file);
			}
		},
		bind: function(){
			$('.dir-container-<?php echo $_SESSION['active_filebrowsers'];?> .file-link').click(fbrowser<?php echo $_SESSION['active_filebrowsers'];?>.click);
		},
	};
	
	$(document).ready(function(){
		fbrowser<?php echo $_SESSION['active_filebrowsers'];?>.bind();
	});
</script>