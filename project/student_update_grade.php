<?
include "utility_functions.php";

$pagetype = 'a';
$sessionid =$_GET["sessionid"];
verify_session($sessionid, $pagetype);
$clientid = $_GET["clientid"];


$sql = "select sid from student where clientid = '$clientid'";

$result_array = execute_sql_in_oracle ($sql);
$result = $result_array["flag"];
$cursor = $result_array["cursor"];

if ($result == false){
  display_oracle_error_message($cursor);
  die("Client Query Failed.");
}
$values = oci_fetch_array($cursor);
$sid=$values[0];

//sid, cid, title, sectionid, credits, grade
$sql = "select * from currently_enrolled_courses where sid = '$sid'";

$result_array = execute_sql_in_oracle ($sql);
$result = $result_array["flag"];
$cursor = $result_array["cursor"];

if ($result == false){
  display_oracle_error_message($cursor);
  die("Client Query Failed.");
}
echo("<form method=\"post\" action=\"admin.php?sessionid=$sessionid\">
<input type=\"submit\" value=\"Go Back\">
</form>
");
echo("<br>Current Schedule");
echo "<table border=1>";
echo "<tr> <th>Title</th> <th>ClassID</th> <th>Section</th> <th>credits</th> <th>grade</th></tr>";


while ($values = oci_fetch_array($cursor)){
	$sid = $values[0];
	$cid = $values[1];
	$title = $values[2];
	$sectionid = $values[3];
	$credits = $values[4];
	$grade= $values[5];
	//echo($grade);
	echo("<tr>" .
		"<td>$title</td> <td>$cid</td> <td>$sectionid</td> <td>$credits</td>" . 
		" <td> <form method=\"post\" action=\"student_update_grade_action.php?sessionid=$sessionid&clientid=$clientid\">
				<input type=\"hidden\" value = \"$sid\" name=\"sid\">
				<input type=\"hidden\" value = \"$sectionid\" name=\"sectionid\">
				<input type=\"hidden\" value = \"$cid\" name=\"cid\">
 				<input type=\"text\"  value = \"$grade\" size=\"1\" maxlength=\"1\" name=\"grade\"> 
				<input type=\"submit\" value=\"Update\"> </form> </td>" .
		"</tr>");
}

?>