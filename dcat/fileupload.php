<?php
  include("config.php");

  include 'simplexlsx.class.php';

  if(!isset($_GET['projectid'])) {
    header("location: home.php");
  } else {
    $projectid = $_GET['projectid'];
    $file = $_FILES["file"]["tmp_name"];
    $filename = $_FILES["file"]["name"];

    $query = "insert into files(name, project_id, status) values('$filename', '$projectid', 'New')";
    pg_query($query) or die('Query failed: ' . pg_last_error());

    $query = "select currval('files_id_seq'::regclass) as file_id";
    $result = pg_query($query) or die('Query failed: ' . pg_last_error());
    $row = pg_fetch_array($result, null, PGSQL_ASSOC);
    $fileid = $row["file_id"];
    echo $fileid;

    $xlsx = new SimpleXLSX($file);

    // output worsheet 1

    list($num_cols, $num_rows) = $xlsx->dimension();

    foreach( $xlsx->rows() as $key=>$value ) {
      if($key>=11) {
        if(!empty($value[1])) {
          $sql="INSERT INTO file_detail (file_id, d_date, full_text, url, domain, page_type, language, country, author)
          values($fileid,'".pg_escape_string($value[3])."','".pg_escape_string($value[6])."','".pg_escape_string($value[7])."','".pg_escape_string($value[8])."','".pg_escape_string($value[10])."','".pg_escape_string($value[11])."','".pg_escape_string($value[15])."','".pg_escape_string($value[22])."')";

          pg_query($sql) or die('Query failed: ' . pg_last_error());
        }
      }
    }
  }

  pg_query("commit");
  pg_free_result($result);
  pg_close($dbconn);
  header("location: runproject.php?projectid=".$projectid);
?>