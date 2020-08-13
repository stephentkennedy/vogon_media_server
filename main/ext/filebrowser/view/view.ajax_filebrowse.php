<?php if(!empty($form) && $form == true){?>
<label>Selected Directory:</label>
<input id="form-dir" type="text" name="dir" value="<?php echo $dir; ?>" readonly>
<?php } 
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
			console.log(dom);
			var dir = dom.getAttribute('data-loc');
			console.log(dir);
			fbrowser.get(dir);
		},
		bind: function(){
			$('.file-link.dir').click(fbrowser.click);
		},
	};
	
	$(document).ready(function(){
		fbrowser.bind();
	});
</script>