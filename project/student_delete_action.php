<?
include "utility_functions.php";

$pagetype = 'a';
$sessionid =$_GET["sessionid"];
verify_session($sessionid, $pagetype);

// suppress php auto warning.
ini_set( "display_errors", 0);  

// obtain input from dept_delete.php
$clientid = $_POST["clientid"];

// Form the sql string and execute it.
$sql = "delete from student where clientid = '$clientid'";
//echo($sql);

$result_array = execute_sql_in_oracle ($sql);
$result = $result_array["flag"];
$cursor = $result_array["cursor"];

if ($result == false){ 
  // Error occured.  Display error-handling interface.
  echo "<B>Deletion Failed.</B> <BR />";

  display_oracle_error_message($cursor);

  die("<i> 

  <form method=\"post\" action=\"admin.php?sessionid=$sessionid\">
  Read the error message, and then try again:
  <input type=\"submit\" value=\"Go Back\">
  </form>

  </i>
  ");
}

// Record deleted.  Go back automatically.
Header("Location:admin.php?sessionid=$sessionid");
?>