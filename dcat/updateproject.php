<?php
	include("config.php");
	include("session.php");

	if($_SERVER["REQUEST_METHOD"] == "POST") {
		$projectname = $_POST['projectname'];
		$themenames = $_POST['themename'];
		$keywordslist = $_POST['keywords'];
		$user = $_SESSION['login_user'];

		if(isset($_GET['projectid'])) {
			$projectid = $_GET['projectid'];

			//deleting projects, themes and keywords
			$query = "delete from keywords where theme_id in (select id from themes where project_id = $projectid)";
			$result = pg_query($query) or die('Query failed: ' . pg_last_error());
			$query = "delete from themes where project_id = $projectid";
			$result = pg_query($query) or die('Query failed: ' . pg_last_error());
			$query = "delete from projects where id = $projectid";
			$result = pg_query($query) or die('Query failed: ' . pg_last_error());

			//inserting into projects
			$query = "insert into projects(name, user_id) values('$projectname', '$user')";
			$result = pg_query($query) or die('Query failed: ' . pg_last_error());

			//finding project_id
			$query = "select currval('projects_id_seq'::regclass) as project_id";
			$result = pg_query($query) or die('Query failed: ' . pg_last_error());
			$row = pg_fetch_array($result, null, PGSQL_ASSOC);
			$projectid = $row["project_id"];

			$counter = 0;

			foreach ($themenames as $theme) {
				//inserting theme
				$query = "insert into themes(name, project_id) values('$theme', $projectid)";
				$result = pg_query($query) or die('Query failed: ' . pg_last_error());

				//finding theme_id
				$query = "select currval('themes_id_seq'::regclass) as theme_id";
				$result = pg_query($query) or die('Query failed: ' . pg_last_error());
				$row = pg_fetch_array($result, null, PGSQL_ASSOC);
				$themeid = $row["theme_id"];

				//inserting keywords
				$query = "insert into keywords(name, theme_id) select regexp_split_to_table(replace('$keywordslist[$counter]',' ',''),','), $themeid";
				$result = pg_query($query) or die('Query failed: ' . pg_last_error());

				$counter++;
			}
		} else {

			//inserting into projects
			$query = "insert into projects(name, user_id) values('$projectname', '$user')";
			$result = pg_query($query) or die('Query failed: ' . pg_last_error());

			//finding project_id
			$query = "select currval('projects_id_seq'::regclass) as project_id";
			$result = pg_query($query) or die('Query failed: ' . pg_last_error());
			$row = pg_fetch_array($result, null, PGSQL_ASSOC);
			$projectid = $row["project_id"];

			$counter = 0;

			foreach ($themenames as $theme) {
				//inserting theme
				$query = "insert into themes(name, project_id) values('$theme', $projectid)";
				$result = pg_query($query) or die('Query failed: ' . pg_last_error());

				//finding theme_id
				$query = "select currval('themes_id_seq'::regclass) as theme_id";
				$result = pg_query($query) or die('Query failed: ' . pg_last_error());
				$row = pg_fetch_array($result, null, PGSQL_ASSOC);
				$themeid = $row["theme_id"];

				//inserting keywords
				$query = "insert into keywords(name, theme_id) select regexp_split_to_table(replace('$keywordslist[$counter]',' ',''),','), $themeid";
				$result = pg_query($query) or die('Query failed: ' . pg_last_error());

				$counter++;
			}

		}

		pg_query("commit");

		pg_free_result($result);

		pg_close($dbconn);

		header("location: home.php");
	}
?>