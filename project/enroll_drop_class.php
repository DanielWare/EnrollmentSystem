<?
include "utility_functions.php";

$pagetype = 's';
$sessionid =$_GET["sessionid"];
$semester = 2015;
verify_session($sessionid, $pagetype);

$cid =$_GET["cid"];
$sid=$_GET["sid"];

$sql = "select * from studentenrolledcourses where cid = '$cid' and sid='$sid' and semester=$semester";
//echo($sql);

$result_array = execute_sql_in_oracle ($sql);
$result = $result_array["flag"];
$cursor = $result_array["cursor"];

if ($result == false){
  display_oracle_error_message($cursor);
  die("Client Query Failed.");
}
$values = oci_fetch_array($cursor);

$sectionid = $values[3];


echo "<table border = 1>";
echo "<tr><th>$cid</th><th>$sectionid</th></tr>";


echo("
    <form method=\"post\" action=\"enroll_drop_class_action.php?sessionid=$sessionid&cid=$cid&sid=$sid&sectionid=$sectionid\">
    <input type=\"submit\" value=\"DROP CLASS\">
    </form>
    ");

echo ("
    <form method=\"post\" action=\"student.php?sessionid=$sessionid\">
    <input type=\"submit\" value=\"Go Back\">
    </form>
    ");

?>