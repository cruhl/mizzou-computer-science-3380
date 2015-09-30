<?php
	include 'functions.php';
	$db = connect_to_database();

	// creates
	pg_prepare($db, 'create_user', 'INSERT INTO lab8.user_info (username) VALUES ($1); INSERT INTO lab8.authentication VALUES ($1, $2, $3);');
	pg_prepare($db, 'create_log', 'INSERT INTO lab8.log (username, ip_address, action) VALUES ($1, $2, $3);');

	// gets
	pg_prepare($db, 'get_user_auth', 'SELECT * FROM lab8.authentication WHERE username = $1;');
	pg_prepare($db, 'get_user_info', 'SELECT * FROM lab8.user_info WHERE username = $1;');
	pg_prepare($db, 'get_user_log', 'SELECT action, ip_address, log_date FROM lab8.log WHERE username = $1;');

	// updates
	pg_prepare($db, 'update_user_info', 'UPDATE lab8.user_info SET description = $2 WHERE username = $1;');
?>