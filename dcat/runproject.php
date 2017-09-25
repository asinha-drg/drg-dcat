<?php
	include('session.php');
?>
<!DOCTYPE html>
<html>
<head>
	<style type="text/css">
		@import url(https://fonts.googleapis.com/css?family=Roboto:300);
		body {
			/*background: #76b852;
			background: -webkit-linear-gradient(right, #76b852, #8DC26F);
			background: -moz-linear-gradient(right, #76b852, #8DC26F);
			background: -o-linear-gradient(right, #76b852, #8DC26F);
			background: linear-gradient(to left, #76b852, #8DC26F);*/
			background: #FFFFFF;
			font-family: "Roboto", sans-serif;
			-webkit-font-smoothing: antialiased;
			-moz-osx-font-smoothing: grayscale;
		}
		header, footer {
			padding: 1em;
			color: white;
			background-color: #663366;
			clear: left;
			text-align: center;
		}
		.topnav {
			overflow: hidden;
			background-color: #663366;
		}
		.topnav li {
			float: right;
			display: block;
			color: #f2f2f2;
			text-align: center;
			padding: 14px 16px;
			text-decoration: none;
			font-size: 17px;
		}
		.topnav li:hover {
			background-color: #4CAF50;
			color: white;
		}
		button {
			font-family: "Roboto", sans-serif;
			text-transform: uppercase;
			outline: 0;
			background: #4CAF50;
			width: 100%;
			border: 0;
			padding: 15px;
			color: #FFFFFF;
			font-size: 14px;
			-webkit-transition: all 0.3 ease;
			transition: all 0.3 ease;
			cursor: pointer;
		}
		button:hover, button:active, button:focus {
			background: #43A047;
		}
		input {
			font-family: "Roboto", sans-serif;
			outline: 0;
			background: #f2f2f2;
			width: 80%;
			border: 0;
			margin: 0 0 15px;
			padding: 15px;
			box-sizing: border-box;
			font-size: 14px;
		}
		.shadow-z-1 {
			-webkit-box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.12), 0 1px 2px 0 rgba(0, 0, 0, 0.24);
			-moz-box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.12), 0 1px 2px 0 rgba(0, 0, 0, 0.24);
			box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.12), 0 1px 2px 0 rgba(0, 0, 0, 0.24);
		}
		/* -- Material Design Table style -------------- */
		.table {
			width: 100%;
			max-width: 100%;
			margin-bottom: 2rem;
			background-color: #fff;
		}
		.table > thead > tr,
		.table > tbody > tr,
		.table > tfoot > tr {
			-webkit-transition: all 0.3s ease;
			-o-transition: all 0.3s ease;
			transition: all 0.3s ease;
		}
		.table > thead > tr > th,
		.table > tbody > tr > th,
		.table > tfoot > tr > th,
		.table > thead > tr > td,
		.table > tbody > tr > td,
		.table > tfoot > tr > td {
			text-align: left;
			padding: 1.6rem;
			vertical-align: top;
			border-top: 0;
			-webkit-transition: all 0.3s ease;
			-o-transition: all 0.3s ease;
			transition: all 0.3s ease;
		}
		.table > thead > tr > th {
			font-weight: 400;
			color: #757575;
			vertical-align: bottom;
			border-bottom: 1px solid rgba(0, 0, 0, 0.12);
		}
		.table > tbody + tbody {
			border-top: 1px solid rgba(0, 0, 0, 0.12);
		}
		.table .table {
			background-color: #fff;
		}
		.table-hover > tbody > tr:hover > td,
		.table-hover > tbody > tr:hover > th {
			background-color: rgba(0, 0, 0, 0.12);
		}
	</style>
	<title>D-CAT - Run Project</title>
</head>
<body>
	<header>
		<h1>D-CAT: Automatic Data Categorizer</h1>
		<hr>
		<div class="topnav">
			<a href="logout.php"><li>Logout</li></a>
			<a href="home.php"><li>Home</li></a>
			<li><?php echo $_SESSION['login_user_name']; ?></li>
		</div>
	</header>
	<br>
<?php
	include("config.php");

	if(!isset($_GET['projectid'])) {
		header("location: home.php");
	} else {
		$projectid = $_GET['projectid'];
		$query = "select name from projects where id = $projectid";
		$result = pg_query($query) or die('Query failed: ' . pg_last_error());
		$row = pg_fetch_array($result, null, PGSQL_ASSOC)
?>
	<div class="table-responsive-vertical shadow-z-1">
		<table id="table" class="table table-hover table-mc-light-blue">
			<tbody>
				<tr>
					<th>Project Name</th>
					<td colspan="2"><?php echo $row["name"] ?></td>
				</tr>
				<tr>
					<form action="fileupload.php?projectid=<?php echo $projectid ?>" method="post"  enctype="multipart/form-data">
						<th>Upload Excel</th>
						<td><input type="file" name="file"></td>
						<td><button>Upload</button></td>
					</form>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="table-responsive-vertical shadow-z-1">
		<table id="table" class="table table-hover table-mc-light-blue">
			<thead>
				<th>File Name</th>
				<th colspan="2">Status</th>
			</thead>
			<tbody>
<?php
		$query = "select id, name, status from files where project_id = $projectid";
		$result = pg_query($query) or die('Query failed: ' . pg_last_error());

		$count = pg_num_rows($result);

		if($count > 0) {
			while($row = pg_fetch_array($result, null, PGSQL_ASSOC)) {
?>
				<tr>
					<th><?php echo $row["name"] ?></th>
					<td><?php echo $row["status"] ?></td>
					<td>
<?php
				if($row["status"] == "New") {
?>
						<button onclick="location.href='processfile.php?projectid=<?php echo $projectid ?>&fileid=<?php echo $row["id"] ?>'">Process File</button>
<?php
				} elseif($row["status"] == "Processing") {
?>
						<button style="background-color: rgba(0, 0, 0, 0.12)" disabled>Download Report</button>
<?php
				} elseif($row["status"] == "Processed") {
?>
						<button onclick="location.href='filedownload.php?projectid=<?php echo $projectid ?>&fileid=<?php echo $row["id"] ?>'">Download Report</button>
<?php
				}
?>
					</td>
				</tr>
<?php
			}
		} else {
?>
				<tr>
					<td colspan="3">No files to display</td>
				</tr>
<?php
		}
	}
	pg_free_result($result);

	pg_close($dbconn);
?>
			</tbody>
		</table>
	</div>
	<footer>&copy;<b>Decision Resources Group</b> - Product Technology Hackathon 2017</footer>
</body>
</html>