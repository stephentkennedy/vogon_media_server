<header>
<h1><?php echo $title.' ('.$release.')'; ?></h1>
</header>
<div class="float right">
	<img src="<?php echo URI.$poster; ?>" class="poster-preview">
</div>
<p><a class="button" href="<?php echo build_slug('watch/'.$id, [], 'media'); ?>"><i class="fa fa-play"></i> Play</a> <a class="button" href="<?php echo build_slug('edit/'.$id, [], 'media'); ?>"><i class="fa fa-pencil"></i> Edit</a></p>
<p><span class="bold">Starring:</span> <?php echo $starring; ?></p>
<p><?php echo nl2br($desc); ?></p>
<p><span class="bold">Director:</span> <?php echo $director; ?></p>
<p><a class="button" href="<?php echo build_slug('watch/'.$id, [], 'media'); ?>"><i class="fa fa-play"></i> Play</a> <a class="button" href="<?php echo build_slug('edit/'.$id, [], 'media'); ?>"><i class="fa fa-pencil"></i> Edit</a></p>