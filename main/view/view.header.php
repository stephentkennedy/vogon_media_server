<!--DOCTYPE HTML-->
<html>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<?php 
			if(!empty($head_tags)){
				foreach($head_tags as $tag){
					echo $tag;
				}
			}
		?>
		<link rel="icon" type="image/png" href="<?php echo URI; ?>/upload/favicon.png">
		<link rel="stylesheet" href="<?php echo URI . '/' . $stylesheet; ?>" type="text/css">
		<link rel="stylesheet" href="<?php echo URI; ?>/fonts/font-awesome.min.css" type="text/css">
	</head>
	<body>
		<div id="video-holder">
			<video id="video-background" loop poster="<?php echo URI; ?>/upload/background_poster.png" muted preload="none">
				<source src="<?php echo URI; ?>/upload/color_loop_web_small.mp4" type="video/mp4">
			</video>
		</div>
		<header class="main">
			<div id="logo"><img src="<?php echo $logo; ?>" title="<?php echo $logo_title; ?>" alt="<?php echo $logo_alt; ?>" ></div>
			<nav id="main-nav" class="row">
				<?php echo $header_nav; ?>
			</nav>
		</header>
		<div id="content">