<?
include "utility_functions.php";

$pagetype = 'a';
$sessionid =$_GET["sessionid"];
$clientid = $_GET["clientid"];
verify_session($sessionid, $pagetype);
$sid = $_POST["sid"];
$cid = $_POST["cid"];
$sectionid= $_POST["sectionid"];
$semester = $_POST["semester"];

$sql = "update studentenrolledcourses set enrollflag = 0 
			where sid = '$sid' and cid = '$cid' and sectionid = $sectionid and semester = $semester";
echo($sql);

$result_array = execute_sql_in_oracle ($sql);
$result = $result_array["flag"];
$cursor = $result_array["cursor"];

if ($result == false){
  display_oracle_error_message($cursor);
  die("Client Query Failed.");
}

//echo("Update Successful <br>");
//die("<br>Click <A HREF = \"student_update_grade.php?sessionid=$sessionid&clientid=$clientid\">here</A> to go back.");
Header("Location:student_update_grade.php?sessionid=$sessionid&clientid=$clientid");
?>