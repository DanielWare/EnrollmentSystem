<?
include "utility_functions.php";

$pagetype = 's';
$sessionid =$_GET["sessionid"];
$semester = 2015;
verify_session($sessionid, $pagetype);

$cid =$_GET["cid"];
$sid=$_GET["sid"];
$sectionid =$_GET["sectionid"];

$sql = "delete from studentenrolledcourses where cid = '$cid' and sectionid = $sectionid and sid='$sid' and semester = $semester";
//echo($sql);

ini_set( "display_errors", 0);  

$result_array = execute_sql_in_oracle ($sql);
$result = $result_array["flag"];
$cursor = $result_array["cursor"];

if ($result == false){ 
  // Error occured.  Display error-handling interface.
  echo "<B>Deletion Failed.</B> <BR />";

  display_oracle_error_message($cursor);

  die("<i> 

  <form method=\"post\" action=\"enroll.php?sessionid=$sessionid\">
  Read the error message, and then try again:
  <input type=\"submit\" value=\"Go Back\">
  </form>

  </i>
  ");
  
}
oci_close($connection);

$sql = "select numstudents from section where cid = '$cid' and sectionid = $sectionid and semester = $semester";

$result_array = execute_sql_in_oracle ($sql);
$result = $result_array["flag"];
$cursor = $result_array["cursor"];

if ($result == false){ 
  // Error occured.  Display error-handling interface.
  echo "<B>Deletion Failed.</B> <BR />";

  display_oracle_error_message($cursor);

  die("<i> 

  <form method=\"post\" action=\"enroll.php?sessionid=$sessionid\">
  Read the error message, and then try again:
  <input type=\"submit\" value=\"Go Back\">
  </form>

  </i>
  ");
  
}
$values = oci_fetch_array($cursor);
$num_stud = $values[0];
oci_close($connection);
//update stud number
$num_stud = $num_stud - 1;

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
?>