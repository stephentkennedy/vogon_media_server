<!--DOCTYPE HTML-->
<html>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="<?php echo URI . '/'.$_SESSION['css']; ?>">
		<link rel="stylesheet" href="<?php echo URI; ?>/css/layout.css">
		<link rel="icon" type="image/png" href="<?php echo URI; ?>/upload/favicon.png">
	</head>
	<body>
		<div id="content">
			<header>Log In</header>
			<form method="post">
				<label for="email">Email</label>
				<input type="email" name="email" id="email" placeholder="Email">
				<label for="password">Password</label>
				<input type="password" name="password" id="password" placeholder="Password">
				<button type="submit">Login</button>
			</form>
		</div>
	</body>
</html>