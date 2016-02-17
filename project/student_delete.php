<?
include "utility_functions.php";

$pagetype = 'a';
$sessionid =$_GET["sessionid"];
verify_session($sessionid, $pagetype);

// Obtain input from department.php
$clientid = $_GET["clientid"];

// Retrieve the tuple to be deleted and display it.
$sql = "select sid, fname, lname, clientid from student where clientid = '$clientid'";
echo($sql);

$result_array = execute_sql_in_oracle ($sql);
$result = $result_array["flag"];
$cursor = $result_array["cursor"];

if ($result == false){ // error unlikely
  display_oracle_error_message($cursor);
  die("Client Query Failed.");
}

if (!($values = oci_fetch_array ($cursor))) {
  // Record already deleted by a separate session.  Go back.
  Header("Location:admin.php?sessionid=$sessionid");
}
oci_free_statement($cursor);

$sid = $values[0];
$fname = $values[1];
$lname = $values[2];

// Display the tuple to be deleted
echo("
  <form method=\"post\" action=\"student_delete_action.php?sessionid=$sessionid\">
  ClientID (Read-only): <input type=\"text\" readonly value = \"$clientid\" name=\"clientid\"> <br /> 
  StudentID: <input type=\"text\" disabled value = \"$sid\" name=\"sid\">  <br />
  First Name: <input type=\"text\" disabled value = \"$fname\" name=\"fname\">  <br />
  Last Name: <input type=\"text\" disabled value = \"$lname\" name=\"lname\">  <br />
  <input type=\"submit\" value=\"Delete\">
  </form>

  <form method=\"post\" action=\"admin.php?sessionid=$sessionid\">
  <input type=\"submit\" value=\"Go Back\">
  </form>
  ");

?>