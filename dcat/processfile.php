<?php
	include("config.php");
	if((isset($_GET['projectid'])) AND (isset($_GET['fileid']))) {
		$projectid = $_GET['projectid'];
		$fileid = $_GET['fileid'];
		$query = "select process_digitel_data($projectid, $fileid)";
		if (!pg_connection_busy($dbconn)) {
			pg_send_query($dbconn,$query) or die('Query failed: ' . pg_last_error());
			$result = pg_get_result($dbconn);
			pg_query("update files set status = 'Processing' where id = $fileid and project_id = $projectid") or die('Query failed: ' . pg_last_error());
			pg_query("commit");
		}
	}
?>