<?php include 'secure.php'; // force https usage ?>

<?php	
	// logged on?
	session_start();
	if (isset($_SESSION['username'])) {
		header('Location: home.php');
		exit();
	}

	// submitting form?
	if (isset($_POST['submit'])) {

		include 'functions.php';

		// try to log in
		$db = connect_to_database();
		$logged_in = attempt_login($db, $_POST['username'], $_POST['password']);

		// success
		if ($logged_in) {
			session_start();
			$_SESSION['username'] = $_POST['username'];

			// log it
			update_log($db, $_SESSION['username'], 'logged in');
			
			header('Location: home.php');
			exit();
		}

		// bad log in
		update_log($db, $_POST['username'], 'bad log in');
		$error = 'Bad username and/or password combination.';
	}
?>


<?php include 'header.php'; ?>

<h1>Log In</h1>
<?php
	// show error message if exists
	if (isset($error)) {
		echo '<div style="color: red;"><p>' . $error . '</p></div>';
	}
?>
<form action="index.php" method="POST">
	<label>
		Username: <input type="text" name="username" />
	</label>	
	<br /><br />
	<label>
		Password: <input type="password" name="password" />
	</label>
	<br /><br />
	<input type="submit" name="submit" value="Log In"> | <a href="registration.php">New Account</a>
</form>
<br />

<?php include 'footer.php'; ?>