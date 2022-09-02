<?php
	$hashword = hash('sha256', $salt.$password);
	return $hashword;
?>