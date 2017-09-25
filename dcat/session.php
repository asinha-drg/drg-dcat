<?php
	include('config.php');
	session_start();

	$user_check = $_SESSION['login_user'];

	$query = "SELECT user_id FROM users WHERE user_id = '$user_check'";
	$result = pg_query($query) or die('Query failed: ' . pg_last_error());

	$row = pg_fetch_array($result, null, PGSQL_ASSOC);

	$login_session = $row['user_id'];

	if(!isset($_SESSION['login_user'])){
		header("location:index.html");
	}
?>