<?php
 if(!empty($form) && $form == 'dir'){?>
<label>Selected Directory:</label>
<input id="form-dir" type="text" name="dir" value="<?php echo $dir; ?>" readonly>
<?php }else if(!empty($form) && $form == 'file'){
?><label>Selected File:</label>
<input id="form-file" type="text" name="file" value="<?php if(!empty($b_file)){ echo $b_file; } ?>" readonly><?php
} 
	echo $preload;
?><script type="text/javascript">
	var fbrowser = {
		form: <?php if(!empty($form) && $form == true){ echo 'true'; }else{ echo 'false'; } ?>,
		get: function(dir){
			$('.dir-container').html('<i class="fa fa-spin fa-cog"></i>');
			$.get('/ajax/ajax_main/filebrowser', {'dir': dir}).done(fbrowser.load);
		},
		load: function(data){
			$('.dir-container').html(data.content);
			if(fbrowser.form == true){
				$('#form-dir').val(data.dir);
			}
			fbrowser.bind();
		},
		click: function(e){
			var dom = e.target;
			var isDir = $(dom).hasClass('dir');
			if(isDir){
				var dir = dom.getAttribute('data-loc');
				console.log(dir);
				fbrowser.get(dir);
			}else if(fbrowser.form == true){
				var file = dom.getAttribute('data-loc');
				$('#form-file').val(file);
			}
		},
		bind: function(){
			$('.file-link').click(fbrowser.click);
		},
	};
	
	$(document).ready(function(){
		fbrowser.bind();
	});
</script>