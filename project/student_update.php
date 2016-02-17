<?
include "utility_functions.php";

$pagetype = 'a';
$sessionid =$_GET["sessionid"];
verify_session($sessionid, $pagetype);

// Verify how we reach here
if (!isset($_POST["update_fail"])) { // from welceomepage.php
  // Get the dnumber, fetch the record to be updated from the database 
  $clientid = $_GET["clientid"];

  // the sql string
  $sql = "select sid, fname, lname, password, aflag, sflag from student natural join myclient where clientid = '$clientid'";
  //echo($sql);

  $result_array = execute_sql_in_oracle ($sql);
  $result = $result_array["flag"];
  $cursor = $result_array["cursor"];

  if ($result == false){
    display_oracle_error_message($cursor);
    die("Query Failed.");
  }

  $values = oci_fetch_array ($cursor);
  oci_free_statement($cursor);

  $sid = $values[0];
  $fname = $values[1];
  $lname = $values[2];
  $password = $values[3];
  $aflag = $values[4];
  $sflag = $values[5];


}
else { // from update_action.php
  // Get the values of the record to be updated directly
  $sid = $_POST["sid"];
  $fname = $_POST["fname"];
  $lname = $_POST["lname"];
  $password = $_POST["password"];
  $aflag = $_POST["aflag"];
  $sflag = $_POST["sflag"];
}
// display the record to be updated.  
echo("
  <form method=\"post\" action=\"student_update_action.php?sessionid=$sessionid\">
  Client ID (Read-only): <input type=\"text\" readonly value = \"$clientid\" size=\"5\" maxlength=\"5\" name=\"clientid\"> <br />
  Student ID : <input type=\"text\" readonly value = \"$sid\" size=\"30\" maxlength=\"10\" name=\"sid\"> <br />
  First Name : <input type=\"text\" value = \"$fname\" size=\"30\" maxlength=\"30\" name=\"fname\">  <br />
  Last Name : <input type=\"text\" value = \"$lname\" size=\"30\" maxlength=\"30\" name=\"lname\">  <br />
  Password : <input type=\"text\" value = \"$password\" size=\"30\" maxlength=\"12\" name=\"password\">  <br />
  Admin : <input type=\"text\" value = \"$aflag\" size=\"5\" maxlength=\"1\" name=\"aflag\">  <br />
  Student : <input type=\"text\" value = \"$sflag\" size=\"5\" maxlength=\"1\" name=\"sflag\">  <br />


  <input type=\"submit\" value=\"Update\">
  <input type=\"reset\" value=\"Reset fields\">
  </form>

  <form method=\"post\" action=\"student_update_grade.php?sessionid=$sessionid&clientid=$clientid\">
  <input type=\"submit\" value=\"Update Grades\">
  </form>

  <form method=\"post\" action=\"student_update_default.php?sessionid=$sessionid\">
  <input type=\"hidden\" value = \"$clientid\" name =\"clientid\">
  <input type=\"submit\" value=\"reset password to default\">
  </form>


  <form method=\"post\" action=\"admin.php?sessionid=$sessionid\">
  <input type=\"submit\" value=\"Go Back\">
  </form>
  ");
?>