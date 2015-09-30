<?php include 'secure.php'; // force https usage ?>

<?php

	// quck and dirty db connect function
	function connect_to_database() {
		return pg_connect('host=dbhost-pgsql.cs.missouri.edu user=ctr9rc password=LDG74VGc');
	}

	// trues to create a user in db
	function attempt_to_create_user($db, $username, $password, $password_confirmation) {
		// collect errors
		$errors = array();

		// trivial validation
		if (strlen($username) < 5) $errors[] = 'Your username must be at least five characters long.';
		if (strlen($password) < 5) $errors[] = 'Your password must be at least five characters long.';
		if ($password !== $password_confirmation) $errors[] = 'The passwords you entered do not match.';

		if (count($errors) > 0) return $errors;

		// create hash
		$salt = sha1(rand());
		$password_hash = sha1($username . $password . $salt);

		// create user info
		pg_prepare($db, 'create_user_info', 'INSERT INTO lab8.user_info (username) VALUES ($1);');
		$result = pg_execute($db, 'create_user_info', array($username));
		if (!$result) {
			$errors[] = 'Could not create user in the database (the username may be taken).';
			return $errors;
		}

		// create user auth
		pg_prepare($db, 'create_user_authentication', 'INSERT INTO lab8.authentication VALUES ($1, $2, $3);');
		$result = pg_execute($db, 'create_user_authentication', array($username, $password_hash, $salt));
		if (!$result) {
			$errors[] = 'Database error.';
			return $errors;
		}

		// user created
		return $errors;
	}

	// tries to login a user
	function attempt_login($db, $username, $password) {
		// pull user auth from table
		pg_prepare($db, 'get_user_auth', 'SELECT * FROM lab8.authentication WHERE username = $1;');
		$result = pg_execute($db, 'get_user_auth', array($username));

		if (!$result) return false;

		// compare hash
		$result = pg_fetch_assoc($result);
		$password_hash = sha1($result['username'] . $password . $result['salt']);
		return $password_hash === $result['password_hash'];
	}	

	// function for logging
	function update_log($db, $username, $action) {
		pg_prepare($db, 'create_log', 'INSERT INTO lab8.log (username, ip_address, action) VALUES ($1, $2, $3);');
		$result = pg_execute($db, 'create_log', array($username, $_SERVER['REMOTE_ADDR'], $action));
	}
?>