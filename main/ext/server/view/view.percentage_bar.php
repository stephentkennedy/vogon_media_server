<div class="percentage-bar">
<div class="inner-bar<?php if($percent >= 90){ echo ' high';}else if($percent <= 10){ echo ' low';}else if($percent >= 60){ echo ' medium';} ?>" style="width: <?php if($percent >= 100){ echo '100%';}else{
echo $percent.'%';} ?>"></div>
<span class="percentage-label"><?php echo $label; ?></span>
</div>
