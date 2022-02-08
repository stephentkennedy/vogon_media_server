
<div class="pageination">
<?php
if(empty($ajax) || $ajax == false){ ?>
<?php if($cur > 2){?>
	<a class="button" href="<?php echo build_slug('page/'.$first, [], 'audio'); ?>"><i class="fa fa-fast-backward" role="button"></i></a>
<?php } if($cur > 1){?>
	<a class="button" href="<?php echo build_slug('page/'.$prev, [], 'audio'); ?>"><i class="fa fa-step-backward" role="button"></i></a>
<?php }?>
	Page: <?php echo $cur; ?> of <?php echo $last; ?>
<?php if ($cur < $last - 1){ ?>
	<a class="button" href="<?php echo build_slug('page/'.$next, [], 'audio'); ?>"><i class="fa fa-step-forward" role="button"></i></a>
<?php } if($cur < $last){?>
	<a class="button" href="<?php echo build_slug('page/'.$last, [], 'audio'); ?>"><i class="fa fa-fast-forward" role="button"></i></a>
<?php } 
}else{
	if($cur > 2){?>
	<button class="button page-change" data-page="<?php echo $first; ?>" role="button"><i class="fa fa-fast-backward"></i></button>
<?php } if($cur > 1){?>
	<button class="button page-change" data-page="<?php echo $prev; ?>" role="button"><i class="fa fa-step-backward"></i></button>
<?php }?>
	Page: <?php echo $cur; ?> of <?php echo $last; ?>
<?php if ($cur < $last - 1){ ?>
	<button class="button page-change" data-page="<?php echo $next; ?>" role="button"><i class="fa fa-step-forward"></i></button>
<?php } if($cur < $last){?>
	<button class="button page-change" data-page="<?php echo $last; ?>" role="button"><i class="fa fa-fast-forward"></i></button>
<?php } } ?>
</div>
