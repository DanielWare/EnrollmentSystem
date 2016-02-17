<?
include "utility_functions.php";

$pagetype = 's';
$sessionid =$_GET["sessionid"];
verify_session($sessionid, $pagetype);

$cid =$_GET["cid"];
$sectionid=$_GET["sectionid"];
$semester = $_GET["semester"];
//check for seat

$sql = "select maxstudents, numstudents from section where cid = '$cid' and sectionid = $sectionid and semester = $semester";
//echo($sql);
$result_array = execute_sql_in_oracle ($sql);
$result = $result_array["flag"];
$cursor = $result_array["cursor"];

if ($result == false){
  display_oracle_error_message($cursor);
  die("Client Query Failed.");
}

$values = oci_fetch_array($cursor);
$max_stud = $values[0];
$num_stud = $values[1];

oci_close ($connection);



if(($max_stud - $num_stud) > 0){
    //seat available get sid then enroll student

    $sql = "select sid from clientsession natural join student where sessionid = '$sessionid'";
    $result_array = execute_sql_in_oracle ($sql);
    $result = $result_array["flag"];
    $cursor = $result_array["cursor"];

    if ($result == false){
      display_oracle_error_message($cursor);
      die("Client Query Failed.");
    }

    $values = oci_fetch_array($cursor);
    $sid = $values[0];
    oci_close ($connection);

    $sql = "insert into studentenrolledcourses values ('$sid', '$cid', $semester, $sectionid, 1, null)";
    //echo($sql);

    ini_set( "display_errors", 0);  

    $connection = oci_connect ("gq039", "cxbnow", "gqiannew2:1521/pdborcl");
    if($connection == false){
      // failed to connect
      display_oracle_error_message(null);
      die("Failed to connect");
    }

    $cursor = oci_parse($connection, $sql);

    if ($cursor == false) {
      display_oracle_error_message($connection);
      oci_close ($connection);
      // sql failed 
      die("SQL Parsing Failed");
    }

    $result = oci_execute($cursor);

    if ($result == false) {
      echo "<B>Class add Failed.</B> <BR />";
      if (is_null($cursor))
        $err = oci_error();
      else
        $err = oci_error($cursor);


      if($err['code'] == 1){
        echo("Currently enrolled in course or previously taken. <br>");
      } else{
        display_oracle_error_message($cursor);
      }
      
      oci_close ($connection);
      die("Click <A HREF = \"enroll.php?sessionid=$sessionid\">here</A> to go back.");

    }

    oci_close ($connection);  

    $return_array["flag"] = $result;
    $return_array["cursor"] = $cursor;

    //adds student
    $num_stud = $num_stud + 1;
    $sql = "update section set numstudents = $num_stud where cid = '$cid' and sectionid = $sectionid and semester = $semester";

    $result_array = execute_sql_in_oracle ($sql);
    $result = $result_array["flag"];
    $cursor = $result_array["cursor"];

    if ($result == false){
      display_oracle_error_message($cursor);
      die("Client Query Failed.");
    }
    oci_commit ($connection);
    oci_close($connection);

    Header("Location:enroll.php?sessionid=$sessionid");

} else {

  echo("No seats available.");
  die("Click <A HREF = \"enroll.php?sessionid=$sessionid\">here</A> to go back.");

}



?>
