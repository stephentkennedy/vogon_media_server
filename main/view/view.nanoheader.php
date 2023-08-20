<!--DOCTYPE HTML-->
<html>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="icon" type="image/png" href="<?php echo build_slug('upload/favicon.png'); ?>">
		<?php 
			if(!empty($head_tags)){
				foreach($head_tags as $tag){
					echo $tag;
				}
			}
		?>
		<?php if(!empty($title)){ ?>
		<title><?php echo $title; ?></title>
		<?php } ?>
	</head>
	<body>