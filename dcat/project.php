<?php
	include('session.php');
?>
<!DOCTYPE html>
<html>
<head>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
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
		textarea {
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
		.table > tbody > tr {
			-webkit-transition: all 0.3s ease;
			-o-transition: all 0.3s ease;
			transition: all 0.3s ease;
		}
		.table > tbody > tr > th,
		.table > tbody > tr > td {
			text-align: left;
			padding: 1.6rem;
			vertical-align: center;
			border-top: 0;
			-webkit-transition: all 0.3s ease;
			-o-transition: all 0.3s ease;
			transition: all 0.3s ease;
		}
		.table > tbody > tr > th {
			width: 25%;
		}
		.table > tbody > tr > td {
			width: 75%;
		}
		.table .table {
			background-color: #fff;
		}
	</style>
	<script type="text/javascript">
		function formSubmit()
		{
			$("#form").submit();
		}
		function addTheme() {
			var text_node_1 = document.createTextNode("Theme Name");
			var text_node_2 = document.createTextNode("Keywords");
			var text_node_3 = document.createTextNode("(comma separated)");

			var input_node = document.createElement("INPUT");
			var input_attrib_1 = document.createAttribute("type");
			input_attrib_1.value = "text";
			input_node.attributes.setNamedItem(input_attrib_1);
			var input_attrib_2 = document.createAttribute("name");
			input_attrib_2.value = "themename[]";
			input_node.attributes.setNamedItem(input_attrib_2);

			var textarea_node = document.createElement("TEXTAREA");
			var textarea_attrib_1 = document.createAttribute("name");
			textarea_attrib_1.value = "keywords[]";
			textarea_node.attributes.setNamedItem(textarea_attrib_1);

			var h6_node = document.createElement("H6");
			h6_node.appendChild(text_node_3);

			var th_node_1 = document.createElement("TH");
			th_node_1.appendChild(text_node_1);
			var td_node_1 = document.createElement("TD");
			td_node_1.appendChild(input_node);

			var tr_node_1 = document.createElement("TR");
			tr_node_1.appendChild(th_node_1);
			tr_node_1.appendChild(td_node_1);

			var th_node_2 = document.createElement("TH");
			th_node_2.appendChild(text_node_2);
			th_node_2.appendChild(h6_node);
			var td_node_2 = document.createElement("TD");
			td_node_2.appendChild(textarea_node);

			var tr_node_2 = document.createElement("TR");
			tr_node_2.appendChild(th_node_2);
			tr_node_2.appendChild(td_node_2);

			document.getElementById("tbody").appendChild(tr_node_1);
			document.getElementById("tbody").appendChild(tr_node_2);
		}
	</script>
	<title>D-CAT - Project Details</title>
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
?>
	<form action="updateproject.php" method="post" id="form">
		<div class="table-responsive-vertical shadow-z-1">
			<table id="table" class="table table-hover table-mc-light-blue">
				<tbody id="tbody">
					<tr>
						<th>Project Name</th>
						<td><input type="text" name="projectname"></td>
					</tr>
					<tr>
						<th>Theme Name</th>
						<td><input type="text" name="themename[]"></td>
					</tr>
					<tr>
						<th>Keywords<h6>(comma separated)</h6></th>
						<td><textarea name="keywords[]"></textarea></td>
					</tr>
				</tbody>
			</table>
		</div>
	</form>
<?php
	} else {
		$projectid = $_GET['projectid'];
		$query = "select name from projects where id = $projectid";
		$result = pg_query($query) or die('Query failed: ' . pg_last_error());
		$row = pg_fetch_array($result, null, PGSQL_ASSOC)
?>
	<form action="updateproject.php?projectid=<?php echo $projectid ?>" method="post" id="form">
		<div class="table-responsive-vertical shadow-z-1">
			<table id="table" class="table table-hover table-mc-light-blue">
				<tbody id="tbody">
					<tr>
						<th>Project Name</th>
						<td><input type="text" name="projectname" value="<?php echo $row["name"] ?>"></td>
					</tr>
<?php
		$query = "select t.name, string_agg(k.name,',') as keywords from themes t inner join keywords k on k.theme_id = t.id where t.project_id = $projectid group by t.name";
		$result = pg_query($query) or die('Query failed: ' . pg_last_error());

		$count = pg_num_rows($result);

		if($count > 0) {
			while($row = pg_fetch_array($result, null, PGSQL_ASSOC)) {
?>
					<tr>
						<th>Theme Name</th>
						<td><input type="text" name="themename[]" value="<?php echo $row["name"] ?>"></td>
					</tr>
					<tr>
						<th>Keywords<h6>(comma separated)</h6></th>
						<td><textarea name="keywords[]"><?php echo $row["keywords"] ?></textarea></td>
					</tr>
<?php
			}
		} else {
?>
					<tr>
						<td colspan="3">No themes to display</td>
					</tr>
<?php
		}
?>
				</tbody>
			</table>
		</div>
	</form>
<?php
	}

	pg_free_result($result);

	pg_close($dbconn);
?>
	<p><button onclick="addTheme()">Add Theme</button>&nbsp;&nbsp;<button onclick="formSubmit();">Submit</button></p>
	<footer>&copy;<b>Decision Resources Group</b> - Product Technology Hackathon 2017</footer>
</body>
</html>