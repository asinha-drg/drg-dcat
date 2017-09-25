<?php
	include("config.php");

	if($_SERVER["REQUEST_METHOD"] == "POST") {
		$user_id = $_POST['username'];
		$name = $_POST['name'];
		$password = $_POST['password'];
		$email = $_POST['email'];

		$query = "insert into users(user_id, name, password, address) values('$user_id', '$name', '$password', '$email')";
		$result = pg_query($query) or die('Query failed: ' . pg_last_error());
		pg_query("commit");

		pg_free_result($result);

		pg_close($dbconn);

		header("location: index.html");
	}
?>