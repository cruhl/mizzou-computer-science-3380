<?php
	// check for https
	if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === '') {
		
		// use https
		echo 'Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		//header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
		exit();
	}
?>