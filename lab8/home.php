<?php include 'secure.php'; // force https usage ?>

<?php
	// logged in?
	session_start();
	if (!isset($_SESSION['username'])) {
		header('Location: index.php');
		exit();
	}
	
	include 'functions.php';
	$db = connect_to_database();

	// pull user info
	pg_prepare($db, 'get_user_info', 'SELECT * FROM lab8.user_info WHERE username = $1;');
	$result = pg_execute($db, 'get_user_info', array($_SESSION['username']));
	$user_info = pg_fetch_assoc($result);

	// get log record for user
	pg_prepare($db, 'get_user_log', 'SELECT action, ip_address, log_date FROM lab8.log WHERE username = $1;');
	$log = pg_execute($db, 'get_user_log', array($_SESSION['username']));
	$row_count = pg_num_rows($log);
?>

<?php include 'header.php'; ?>

<h1>Hello, <?php echo $_SESSION['username']; ?></h1>
<p>IP Address: <strong><?php echo $_SERVER['REMOTE_ADDR']; ?></strong></p>
<p>Registration Date: <strong><?php echo $user_info['registration_date']; ?></strong></p>
<p>Description: <strong><?php echo $user_info['description']; ?></strong></p>
<p>Number of items in log: <strong><?php echo $row_count; ?></strong></p>

<?php
	// render table
	echo '<table border="1">';

	// render headers
	$row = pg_fetch_assoc($log, 0);
	echo '<tr>';
	foreach ($row as $column_value => $row_value) {
		echo '<th>' . $column_value . '</th>';
	}
	echo '</tr>';

	// render table values
	while ($row = pg_fetch_assoc($log)) {
		echo '<tr>';
		foreach ($row as $column_value => $row_value) {
			echo '<td>' . $row_value . '</td>';
		}
		echo '</tr>';
	}
	echo '</table>';
?>

<p>
	<a href="update.php">Update Info</a> | <a href="logout.php">Log Out</a>
</p>

<?php include 'footer.php'; ?>