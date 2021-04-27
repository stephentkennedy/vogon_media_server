<header>
<h1><?php echo $title.' ('.$release.')'; ?></h1>
</header>
<div class="float right">
	<img src="<?php echo URI.$poster; ?>" class="poster-preview">
</div>
<p><a class="button" href="<?php echo build_slug('watch/'.$id, [], 'media'); ?>"><i class="fa fa-play"></i> Play</a> <a class="button" href="<?php echo build_slug('edit/'.$id, [], 'media'); ?>"><i class="fa fa-pencil"></i> Edit</a></p>
<p><span class="bold">Starring:</span> <?php
$starring = explode(',', $starring);
foreach($starring as $key => $star){
	$star = trim($star);
	$string = '<a class="open-popup" data-title="Titles With: '.$star.'" data-src="'.build_slug('ajax/star_search/media', ['star'=> $star]).'">'.$star.'</a>';
	$starring[$key] = $string;
}
$starring = implode(' ',$starring);
echo $starring;
?></p>
<p><?php echo nl2br($desc); ?></p>
<p><span class="bold">Director:</span> <?php
	$director = explode(',', $director);
	foreach($director as $key => $d){
		$d = trim($d);
		$string =  '<a class="open-popup" data-title="Titles Directed By: '.$d.'" data-src="'.build_slug('ajax/director_search/media', ['director'=> $d]).'">'.$d.'</a>';
		$director[$key] = $string;
	}
 echo implode(' ', $director); ?></p>
<p><a class="button" href="<?php echo build_slug('watch/'.$id, [], 'media'); ?>"><i class="fa fa-play"></i> Play</a> <a class="button" href="<?php echo build_slug('edit/'.$id, [], 'media'); ?>"><i class="fa fa-pencil"></i> Edit</a></p>