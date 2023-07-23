<!DOCTYPE html>
<html>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1" user-scalable="no">
		<?php 
			if(!empty($head_tags)){
				foreach($head_tags as $tag){
					echo $tag;
				}
			}
		?>
		<link rel="icon" type="image/png" href="<?php echo URI; ?>/upload/favicon.png">
		<link rel="stylesheet" href="<?php echo URI . '/' . $stylesheet . '?version=' . VER; ?>" type="text/css">
		<link rel="stylesheet" href="<?php echo URI; ?>/fonts/font-awesome.min.css" type="text/css">
		<?php if(!empty($title)){ ?>
		<title><?php echo $title; ?></title>
		<?php } ?>
	</head>
	<body>
		<?php if(!isset($_SESSION['bg_vid_bool']) || $_SESSION['bg_vid_bool'] == 1){ 
		if(isset($_SESSION['bg_vid'])){
			$mime = mime_content_type($_SESSION['bg_vid']);
			$location = str_replace(ROOT, URI, $_SESSION['bg_vid']);
		}else{
			$mime = 'video/mp4';
			$location = URI.'/upload/color_loop_web_small.mp4';
		}
		
		?>
		<div id="video-holder">
			<?php 
			$mime_test = explode('/', $mime);
			switch($mime_test[0]){
				case 'video':
			?><video id="video-background" loop muted preload="none">
				<source src="<?php echo $location; ?>" type="<?php echo $mime; ?>">
			</video><?php
				break;
				case 'image':
				?><img id="video-background" src="<?php echo $location; ?>"><?php
					break;
			}
			?>
		</div>
		<?php } ?>
		<header class="main">
			<div id="logo"><img src="<?php echo $logo; ?>" title="<?php echo $logo_title; ?>" alt="<?php echo $logo_alt; ?>" ><?php if(!empty($logo_text)){
				echo '<span id="logo-text">'.$logo_text.'</span>';
			} ?></div>
			<nav id="main-nav" class="row">
				<?php echo $header_nav; ?>
			</nav>
		</header>
		<div id="content">
