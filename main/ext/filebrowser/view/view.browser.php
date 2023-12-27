<script type="text/javascript">

	var file_controller = {
		lookup_url: '<?php echo build_slug('ajax/ajax_file_lookup/filebrowser'); ?>',
		lookup: function(file){
			var data = {
				search: file
			};
			$.post(file_controller.lookup_url, data, function(returned){
				if(typeof returned.error != 'undefined'){
					var win = aPopup.newWindow(returned.error, {title: 'Error'});
					return win;
				}
				var string = '<span>' + returned.record.data_name + '</span><br><a href="' + returned.link + '">Open</a> <a href="' + returned.link + '" target="_blank">Open in New Tab</a>';
				var win = aPopup.newWindow(string);
			});
		}
	};

	$(document).ready(function(){
		$('.file-link.dir').click(function(){
			var dir = $(this).data('loc');
			var url = new URL(window.location);
			window.location = url.pathname + '?dir=' + dir;
		});
		$('.file-link.file').click(function(){
			var $this = $(this);
			var file = $this.data('loc');
			file_controller.lookup(file);
		});
	});
</script>