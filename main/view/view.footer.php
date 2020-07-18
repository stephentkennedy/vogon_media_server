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
				backgroundVid.playbackRate = 0.3;
				backgroundVid.play();
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
	</body>
</html>