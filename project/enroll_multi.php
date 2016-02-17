<?
include "utility_functions.php";

$pagetype = 's';
$sessionid =$_GET["sessionid"];
verify_session($sessionid, $pagetype);


echo("
	<form method=\"post\" action=\"enroll_multi_action.php?sessionid=$sessionid\">
	ClassID: <input type=\"text\" value = \"$cid1\" size=\"6\" maxlength=\"6\" name=\"cid1\"> 
	SectionID: <input type=\"text\" value = \"$sectionid1\" size=\"6\" maxlength=\"6\" name=\"sectionid1\"> <br>
	ClassID: <input type=\"text\" value = \"$cid2\" size=\"6\" maxlength=\"6\" name=\"cid2\"> 
	SectionID: <input type=\"text\" value = \"$sectionid2\" size=\"6\" maxlength=\"6\" name=\"sectionid2\"> <br>
	ClassID: <input type=\"text\" value = \"$cid3\" size=\"6\" maxlength=\"6\" name=\"cid3\"> 
	SectionID: <input type=\"text\" value = \"$sectionid3\" size=\"6\" maxlength=\"6\" name=\"sectionid3\"> <br>
	ClassID: <input type=\"text\" value = \"$cid4\" size=\"6\" maxlength=\"6\" name=\"cid4\"> 
	SectionID: <input type=\"text\" value = \"$sectionid4\" size=\"6\" maxlength=\"6\" name=\"sectionid4\"> <br>
	ClassID: <input type=\"text\" value = \"$cid5\" size=\"6\" maxlength=\"6\" name=\"cid5\"> 
	SectionID: <input type=\"text\" value = \"$sectionid5\" size=\"6\" maxlength=\"6\" name=\"sectionid5\"> <br>
	
	<input type=\"submit\" value=\"Add Classes\">
	</form>


	<form method=\"post\" action=\"enroll.php?sessionid=$sessionid\">
  	<input type=\"submit\" value=\"Go Back\">
  	</form>
	");

?>