<script type="text/javascript">
	$(document).ready(function(){
		$('.file-link.dir').click(function(){
			var dir = $(this).data('loc');
			var url = new URL(window.location);
			window.location = url.pathname + '?dir=' + dir;
		});
	});
</script>