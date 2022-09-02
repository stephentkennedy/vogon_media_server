<canvas id="vis"></canvas>
<audio class="player" controls>
	<source src="<?php echo $track; ?>" type="<?php echo $mime_type; ?>">
</audio>
<script type="text/javascript" src="<?php echo URI;?>/js/visualizer.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	initPage();
});
</script>