			<?php
			global $user_model;
			if($user_model->permission('sys_info')){
			?>
			<div id="server-status-tray" class="tray right col col-three">
				<div class="controls">
					<i class="fa fa-tachometer tray-expand" title="Status"></i>
				</div>
				<div class="container">
					<h2>Server Status</h2>
					<div id="server-status" class="content row">
					</div>
				</div>
			</div>
			<?php } ?>
		</div><!-- End of #content Div -->
		<div id="popup"></div>
		<footer>
			<nav id="footer-nav" class="row">
				<?php echo $footer_nav; ?>
			</nav>
		</footer>
		<script type="text/javascript">
			$(document).ready(function(){
				$('.open-popup').click(function(){
					var o = $(this);
					var src = o.attr('data-src');
					var title = o.attr('data-title');
					$.post(src, {method: 'ajax'}, function(data){
						aPopup.newWindow(data, {
							title: title,
							height: 300
						});
					});
				});
				var backgroundVid = $('#video-background')[0];
				if(backgroundVid != undefined){
					backgroundVid.playbackRate = 0.3;
					backgroundVid.play();
				}
				$.post("<?php echo build_slug('ajax/ajax_top/media');?>", function(returned){
					var string = '' + returned + '';
					$('#server-status-tray #server-status').html(string);
				});
				setInterval(function(){
					if($('#server-status-tray').hasClass('open')){
						$.post("<?php echo build_slug('ajax/ajax_top/media');?>", function(returned){
							var string = '' + returned + '';
							$('#server-status-tray #server-status').html(string);
						});
					}
				}, 5000);
			});
			<?php 
				//Easiest way to clear out get variables so we don't have to worry about them later.
				if(isset($_GET['force_reload']) && $_GET['force_reload'] == 'true'){
					echo 'window.location = "'.explode('?', $_SERVER['REQUEST_URI'])[0].'";';
				}
			?>
		</script>
		<script type="text/javascript">
			$(document).ready(function(){
				var varSecs = $('.variable-section');
				varSecs.each(function(){
					var sec = $(this);
					var id = sec.attr('data-id');
					var val = sec.attr('data-value');
					var item = $('#' + id);
					console.log(item);
					item.on('keyup change mouseup',function(){
						var type = item.attr('type');
						var cur_val = item.val();
						switch(type){
							case 'checkbox':
							case 'radio':
								console.log(item[0]);
								if(item[0].checked){
									sec.addClass('active');
								}else{
									sec.removeClass('active');
								}
								break;
							default:
								console.log(type);
								if(cur_val == val){
									sec.addClass('active');
								}else{
									sec.removeClass('active');
								}
								break;
								break;
						}
					});
				});
			});
		</script>
		<script type="text/javascript" src="<?php echo URI; ?>/js/app.js"></script>
		<script type="text/javascript" src="<?php echo URI; ?>/js/trays.js"></script>
	</body>
</html>
