<?php include 'secure.php'; // force https usage ?>

<?php
	session_start();
	if (!isset($_SESSION['username'])) {
		header('Location: index.php');
		exit();
	}
	
	include 'functions.php';
	$db = connect_to_database();

	// updating?
	if (isset($_POST['submit'])) {

		// execute update
		pg_prepare($db, 'update_description', 'UPDATE lab8.user_info SET description = $2 WHERE username = $1;');
		$result = pg_execute($db, 'update_description', array($_SESSION['username'], $_POST['description']));
		
		update_log($db, $_SESSION['username'], 'updated user info');

		header('Location: home.php');
		exit();
	}

	// pull user info
	pg_prepare($db, 'get_user_info', 'SELECT * FROM lab8.user_info WHERE username = $1;');
	$result = pg_execute($db, 'get_user_info', array($_SESSION['username']));
	$user_info = pg_fetch_assoc($result);
?>

<?php include 'header.php'; ?>

<h1><?php echo $_SESSION['username']; ?>'s Info</h1>
<form action="update.php" method="POST">
	Description:<br />
	<textarea name="description"><?php echo $user_info['description']; ?></textarea>
	<br /><br />
	<input type="submit" name="submit" value="Update"> | <a href="home.php">Cancel</a>
</form>

<?php include 'footer.php'; ?>