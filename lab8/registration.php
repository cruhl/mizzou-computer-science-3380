<?php include 'secure.php'; // force https usage ?>

<?php
	if (isset($_POST['submit'])) {

		include 'functions.php';

		$db = connect_to_database();
		$errors = attempt_to_create_user($db,
			$_POST['username'],
			$_POST['password'],
			$_POST['password_confirmation']
		);

		// good, log in
		if (count($errors) === 0) {
			session_start();
			$_SESSION['username'] = $_POST['username'];

			update_log($db, $_SESSION['username'], 'user created');

			header('Location: home.php');
			exit();
		}
	}
?>

<?php include 'header.php'; ?>

<h1>Register</h1>
<?php
	// show errors
	if (isset($errors)) {
		echo '<div style="color: red;">';
		foreach ($errors as $error) {
			echo '<p>' . $error . '</p>';
		}
		echo '</div>';
	}
?>
<form action="registration.php" method="POST">
	<label>
		Username: <input type="text" name="username" />
	</label>	
	<br /><br />
	<label>
		Password: <input type="password" name="password" />
	</label>
	<br /><br />	
	<label>
		Password Confirmation: <input type="password" name="password_confirmation" />
	</label>
	<br /><br />
	<input type="submit" name="submit" value="Create Account"> | <a href="index.php">Cancel</a>
</form>
<br />

<?php include 'footer.php'; ?>