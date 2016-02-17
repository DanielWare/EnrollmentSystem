<?
include "utility_functions.php";

$sessionid =$_GET["sessionid"];
verify_session($sessionid);

// Here we can generate the content of the welcome page
echo("Welcome! <br />");
echo("<UL>
  <LI><A HREF=\"admin.php?sessionid=$sessionid\">Admin</A></LI>
  <LI><A HREF=\"student.php?sessionid=$sessionid\">Student</A></LI>
  <LI><A HREF=\"change_password.php?sessionid=$sessionid\">Change Password</A></LI>
  <LI><A HREF=\"search_sections.php?sessionid=$sessionid\">Search Sections</A></LI>
  </UL>");

echo("<br />");
echo("<br />");
echo("Click <A HREF = \"logout_action.php?sessionid=$sessionid\">here</A> to Logout.");

?>