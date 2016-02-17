<?
include "utility_functions.php";

$pagetype = 'a';
$sessionid =$_GET["sessionid"];
verify_session($sessionid, $pagetype);


// Obtain the inputs from dept_add_action.php
$clientid = $_POST["clientid"];
$fname = $_POST["fname"];
$lname = $_POST["lname"];

// display the insertion form.
echo("
  <form method=\"post\" action=\"student_add_action.php?sessionid=$sessionid\">
  First Name (Required): <input type=\"text\" value = \"$fname\" size=\"50\" maxlength=\"30\" name=\"fname\">  <br />
  Last Name (Required): <input type=\"text\" value = \"$lname\" size=\"50\" maxlength=\"30\" name=\"lname\">  <br />
  ClientID: <input type=\"text\" value = \"$clientid\" size=\"50\" maxlength=\"8\" name=\"clientid\">  <br />
  password: <input type=\"text\" value = \"$password\" size=\"50\" maxlength=\"12\" name=\"password\">  <br />
  Student? (1/0): <input type=\"text\" value = \"$aflag\" size=\"50\" maxlength=\"1\" name=\"sflag\">  <br />
  Admin? (1/0): <input type=\"text\" value = \"$sflag\" size=\"50\" maxlength=\"1\" name=\"aflag\">  <br />

  Age: <input type=\"text\" value = \"$age\" size=\"50\" maxlength=\"2\" name=\"age\">  <br />
  Street #: <input type=\"text\" value = \"$streetnumber\" size=\"50\" maxlength=\"30\" name=\"streetnumber\">  <br />
  Street Name: <input type=\"text\" value = \"$streetname\" size=\"50\" maxlength=\"50\" name=\"streetname\">  <br />
  City: <input type=\"text\" value = \"$city\" size=\"50\" maxlength=\"30\" name=\"city\">  <br />
  State: <input type=\"text\" value = \"$state\" size=\"50\" maxlength=\"30\" name=\"state\">  <br />
  Zipcode: <input type=\"text\" value = \"$zipcode\" size=\"50\" maxlength=\"7\" name=\"zipcode\">  <br />
  Type (u/g): <input type=\"text\" value = \"$type\" size=\"50\" maxlength=\"1\" name=\"type\">  <br />


  <input type=\"submit\" value=\"Add\">
  <input type=\"reset\" value=\"Reset to Original Value\">
  </form>

  <form method=\"post\" action=\"admin.php?sessionid=$sessionid\">
  <input type=\"submit\" value=\"Go Back\">
  </form>");

?>