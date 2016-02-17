<?
include "utility_functions.php";

$sessionid =$_GET["sessionid"];
verify_session($sessionid);

//change password for self

echo"Change Password";
$sql = "select * from myclient natural join clientsession where sessionid='$sessionid'";
//echo ($sql);

$result_array = execute_sql_in_oracle ($sql);
$result = $result_array["flag"];
$cursor = $result_array["cursor"];

if ($result == false){
  display_oracle_error_message($cursor);
  die("Client Query Failed.");
}
$values = oci_fetch_array($cursor);

$clientid = $values[0];
$password = $values[1];

echo("
  <form method=\"post\" action=\"change_password_action.php?sessionid=$sessionid\">
  ClientID (read-only): <input type=\"text\" readonly value = \"$clientid\" size=\"50\" maxlength=\"8\" name=\"clientid\">  <br />
  password (change this): <input type=\"text\" value = \"$password\" size=\"50\" maxlength=\"12\" name=\"password\">  <br />

  <input type=\"submit\" value=\"Change Password\">
  <input type=\"reset\" value=\"Reset Field\">
  </form>

  <form method=\"post\" action=\"welcomepage.php?sessionid=$sessionid\">
  <input type=\"submit\" value=\"Go Back\">
  </form>");


oci_free_statement($cursor);

echo "</table>";
?>