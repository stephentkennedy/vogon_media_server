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
		<title><?php if(!empty($title)){ echo $title; } ?></title>
	</head>
	<body>