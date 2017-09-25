<?php
   include("config.php");
   session_start();

   if($_SERVER["REQUEST_METHOD"] == "POST") {
      $user_id = $_POST['username'];
      $password = $_POST['password'];

      $query = "select name from users where user_id = '$user_id' and password = '$password'";
      $result = pg_query($query) or die('Query failed: ' . pg_last_error());

      $row = pg_fetch_array($result, null, PGSQL_ASSOC);

      $count = pg_num_rows($result);

      if($count == 1) {
         $_SESSION['login_user'] = $user_id;
         $_SESSION['login_user_name'] = $row['name'];
         header("location: home.php");
      } else {
         header("location: index.html");
      }
   }
?>