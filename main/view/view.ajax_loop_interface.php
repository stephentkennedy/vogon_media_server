<?php 
	if(empty($ext)){
		$ext = false;
	}
?>
<div id="progress_bar_container">
	<div id="progress_message"></div>
	<div style="width: 0%;" id="progress_bar"></div>
</div>
<div id="messages"></div>
<style>
	#progress_bar_container{
		width: 100%;
		position: relative;
		height: 3rem;
		background: #aaa;
	}
	#progress_bar{
		background: green;
		height: 100%;
		position: absolute;
		left: 0;
		top: 0;
		z-index: 0;
	}
	#progress_message{
		position: relative;
		padding: 1rem;
		z-index: 10;
	}
	#messages{
		max-height: 50vh;
		margin-top: 1rem;
		padding: 1rem;
		border: 1px solid #fff;
		overflow: auto;
	}
</style>
<script type="text/javascript">
	var ajax_loop_interface = {
		debug: true,
		base_url: <?php echo "'".build_slug($route, [], $ext)."'"; ?>,
		i: 0,
		progress: function(){
			var total = Number($('#progress_bar').attr('data-total'));
			var percent = ~~((this.i / total) * 100);
			$('#progress_bar').css('width', percent + '%');
			$('#progress_message').html(this.i + ' / ' + total + ' ( ' + percent + '% )');
			this.i++;
		},
		output: function(output){
			if(output != undefined && output != null && output != ''){
				$('#messages').append(output + '<br>');
				$('#messages').scrollTop($('#messages')[0].scrollHeight);
			}
		},
		get: function(q){
			if(q == undefined){
				var url = this.base_url;
				this.output('Initializing.');
			}else{
				if(this.base_url.search(/\?/g) === -1){
					var url = this.base_url + q;
				}else{
					//If we already have an item or items we're appending we'll switch out the leading character to make it valid.
					var url = this.base_url + q.replace('?', '&');
				}
			}
			$.get(url, []).done(function(returned){
				ajax_loop_interface.loop(returned);
			}).fail(function(returned){
				ajax_loop_interface.output('Unable to make request.');
				console.log(returned);
			});
		},
		loop: function(data){
			if(this.debug == true){
				console.log(data);
			}
			switch(data.state){
				case 'finished':
					this.output(data.message);
					if(data.cleanup != undefined){
						this.output(data.cleanup);
					}
					this.progress();
					this.output('<span style="color:green;font-weight:bold;">Finished!</span>');
					break;
				case 'error':
					this.output('<span style="color:red;font-weight:bold;">ERROR</span>: ' + data.error);
					break;
				case 'initialized':
					$('#progress_bar').attr('data-total', data.total_tasks);
					this.output('Initialized and starting.');
					//This isn't missing a break, we want it to do this and the loop logic below.
				default:
					if(data.continue == true){
						this.output(data.message);
						this.progress();
						this.get(data.next_q);
					}
					break;
			}
		}
	};
	$(document).ready(function(){
		$.ajaxSetup({
			timeout: 0
		});
		ajax_loop_interface.get();
	});
</script>