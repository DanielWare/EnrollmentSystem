<?
include "utility_functions.php";

$pagetype = 'a';
$sessionid =$_GET["sessionid"];
verify_session($sessionid, $pagetype);

// Suppress PHP auto warning.
ini_set( "display_errors", 0);  

// Get input from dept_update.php and update the record.
$clientid = $_POST["clientid"];
$sid = $_POST["sid"];
$fname = $_POST["fname"];
$lname = $_POST["lname"];

// the sql string
$sql = "update student set fname = '$fname', lname = '$lname', sid = '$sid' where clientid = '$clientid'";
//echo($sql);

$result_array = execute_sql_in_oracle ($sql);
$result = $result_array["flag"];
$cursor = $result_array["cursor"];

if ($result == false){
  // Error handling interface.
  echo "<B>Update Failed.</B> <BR />";

  display_oracle_error_message($cursor);

  die("<i> 

  <form method=\"post\" action=\"student_update?sessionid=$sessionid\">

  <input type=\"hidden\" value = \"$clientid\" name=\"clientid\">
  <input type=\"hidden\" value = \"$sid\" name=\"sid\">
  <input type=\"hidden\" value = \"$fname\" name=\"fname\">
  <input type=\"hidden\" value = \"$lname\" name=\"lname\">
  <input type=\"hidden\" value = \"1\" name=\"update_fail\">
  
  Read the error message, and then try again:
  <input type=\"submit\" value=\"Go Back\">
  </form>

  </i>
  ");
}


$password = $_POST["password"];
$aflag = $_POST["aflag"];
$sflag = $_POST["sflag"];

$sql = "update myclient set password ='$password', aflag='$aflag', sflag='$sflag' where clientid = '$clientid'";
//echo($sql);

$result_array = execute_sql_in_oracle ($sql);
$result = $result_array["flag"];
$cursor = $result_array["cursor"];

if ($result == false){
  // Error handling interface.
  echo "<B>Update Failed.</B> <BR />";

  display_oracle_error_message($cursor);

  die("<i> 

  <form method=\"post\" action=\"student_update?sessionid=$sessionid\">

  <input type=\"hidden\" value = \"$clientid\" name=\"clientid\">
  <input type=\"hidden\" value = \"$sid\" name=\"sid\">
  <input type=\"hidden\" value = \"$fname\" name=\"fname\">
  <input type=\"hidden\" value = \"$lname\" name=\"lname\">
  <input type=\"hidden\" value = \"1\" name=\"update_fail\">
  
  Read the error message, and then try again:
  <input type=\"submit\" value=\"Go Back\">
  </form>

  </i>
  ");
}


// Record updated.  Go back.
Header("Location:admin.php?sessionid=$sessionid");
?>