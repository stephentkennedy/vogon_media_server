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
		<link rel="icon" type="image/png" href="<?php echo build_slug('upload/favicon.png'); ?>">
		<link rel="stylesheet" href="<?php echo build_slug($stylesheet); ?>" type="text/css">
		<link rel="stylesheet" href="<?php echo build_slug('fonts/font-awesome.min.css'); ?>" type="text/css">
		<?php if(!empty($title)){ ?>
		<title><?php echo $title; ?></title>
		<?php } ?>
	</head>
	<body>