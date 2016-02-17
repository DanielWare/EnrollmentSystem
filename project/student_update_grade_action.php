<?
include "utility_functions.php";

$pagetype = 'a';
$sessionid =$_GET["sessionid"];
$clientid = $_GET["clientid"];
verify_session($sessionid, $pagetype);
$sid = $_POST["sid"];
$cid = $_POST["cid"];
$sectionid= $_POST["sectionid"];
$semester = 2015;
$grade = $_POST["grade"];

$sql = "update studentenrolledcourses set grade = $grade 
			where sid = '$sid' and cid = '$cid' and sectionid = $sectionid and semester = $semester";
//echo($sql);

$result_array = execute_sql_in_oracle ($sql);
$result = $result_array["flag"];
$cursor = $result_array["cursor"];

if ($result == false){
  display_oracle_error_message($cursor);
  die("Client Query Failed.");
}

$sql = "begin update_gpa('$sid'); " .
		"end;";
$result_array = execute_sql_in_oracle ($sql);
$result = $result_array["flag"];
$cursor = $result_array["cursor"];

if ($result == false){
  display_oracle_error_message($cursor);
  die("Client Query Failed.");
}

echo("<form method=\"post\" action=\"student_update_enroll.php?sessionid=$sessionid&clientid=$clientid\">
<input type=\"hidden\" value = \"$sid\" name=\"sid\">
<input type=\"hidden\" value = \"$sectionid\" name=\"sectionid\">
<input type=\"hidden\" value = \"$cid\" name=\"cid\">
<input type=\"hidden\" value = \"$semester\" name=\"semester\">
<input type=\"submit\" value=\"Finalize Class\">
</form>
");


echo("Update Successful <br>");
die("<br>Click <A HREF = \"student_update_grade.php?sessionid=$sessionid&clientid=$clientid\">here</A> to go back.");
//Header("Location:student_update_grade.php?sessionid=$sessionid&clientid=$clientid");
?>