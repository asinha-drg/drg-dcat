<?php
	$dbconn = pg_connect("host=localhost dbname=digital_data user=postgres password=postgres")
    	or die('Could not connect: ' . pg_last_error());
?>