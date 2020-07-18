
<div class="pageination">
<?php
if(empty($ajax) || $ajax == false){ ?>
<?php if($cur > 2){?>
	<a class="button" href="<?php echo build_slug('page/'.$first, [], 'audio'); ?>"><i class="fa fa-fast-backward"></i></a>
<?php } if($cur > 1){?>
	<a class="button" href="<?php echo build_slug('page/'.$prev, [], 'audio'); ?>"><i class="fa fa-step-backward"></i></a>
<?php }?>
	Page: <?php echo $cur; ?> of <?php echo $last; ?>
<?php if ($cur < $last - 1){ ?>
	<a class="button" href="<?php echo build_slug('page/'.$next, [], 'audio'); ?>"><i class="fa fa-step-forward"></i></a>
<?php } if($cur < $last){?>
	<a class="button" href="<?php echo build_slug('page/'.$last, [], 'audio'); ?>"><i class="fa fa-fast-forward"></i></a>
<?php } 
}else{
	if($cur > 2){?>
	<a class="button page-change" data-page="<?php echo $first; ?>"><i class="fa fa-fast-backward"></i></a>
<?php } if($cur > 1){?>
	<a class="button page-change" data-page="<?php echo $prev; ?>"><i class="fa fa-step-backward"></i></a>
<?php }?>
	Page: <?php echo $cur; ?> of <?php echo $last; ?>
<?php if ($cur < $last - 1){ ?>
	<a class="button page-change" data-page="<?php echo $next; ?>"><i class="fa fa-step-forward"></i></a>
<?php } if($cur < $last){?>
	<a class="button page-change" data-page="<?php echo $last; ?>"><i class="fa fa-fast-forward"></i></a>
<?php } } ?>
</div>