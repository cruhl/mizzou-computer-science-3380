
<?php include 'secure.php'; // force https usage ?>

<?php
	session_start();

	// log
	include 'functions.php';
	$db = connect_to_database();
	update_log($db, $_SESSION['username'], 'logged out');

	// kill session info
	unset($_SESSION['username']);
	session_destroy();
	header('Location: index.php');
	exit();
?>