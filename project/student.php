<?
include "utility_functions.php";

$pagetype = 's';
$sessionid =$_GET["sessionid"];
verify_session($sessionid, $pagetype);

$sql = "begin update_gpa('$sid'); " .
		"end;";
//echo($sql);
$result_array = execute_sql_in_oracle ($sql);
$result = $result_array["flag"];
$cursor = $result_array["cursor"];

if ($result == false){
  display_oracle_error_message($cursor);
  die("Client Query Failed.");
}


$sql = "select sid, fname, lname, age, streetNumber, streetName, city, state, zipCode, typeflag, probationflag, clientid from student natural join clientsession where sessionid = '$sessionid'"; //streetNumber, streetName, city, state, zipCode, typeflag, probationflag from student natural join clientsession";
//echo($sql);

$result_array = execute_sql_in_oracle ($sql);
$result = $result_array["flag"];
$cursor = $result_array["cursor"];

if ($result == false){
  display_oracle_error_message($cursor);
  die("Client Query Failed.");
}
$values = oci_fetch_array($cursor);
if($values){
	$sid = $values[0];
	$fname = $values[1];
	$lname = $values[2];
	$age = $values[3];
	$streetNumber = $values[4];
	$streetName = $values[5];
	$city = $values[6];
	$state = $values[7];
	$zipCode = $values[8];
	$typeflag = $values[9];
	$probationflag = $values[10];
	$clientid = $values[11];
}

if($typeflag == 'u'){
	$typeflag = 'undergraduate';
} else{
	$typeflag = 'graduate';
}

if($probationflag == '0'){
	$probationflag = 'no probation';
} else {
	$probationflag = 'on probation';
}

echo ("Welcome " . "$fname" . " " . "$lname" . "<br>");
echo "<table border =1>";
echo ("<tr><th>IDNumber </th> <th>$sid</th></tr>" .
	"<tr><th>age </th> <th>$age</th></tr>" .
	"<tr><th>address </th> <th>$streetNumber $streetName $city , $state $zipCode </th></tr>" .
	"<tr><th>student type </th> <th>$typeflag </th></tr>" .
	"<tr><th>probation status </th> <th>$probationflag </th></tr>");
echo "</table>";

oci_free_statement($cursor);


echo ("
	<form method=\"post\" action=\"enroll.php?sessionid=$sessionid\">
	<input type=\"submit\" value=\"Enrollment\">
  	</form>

	<form method=\"post\" action=\"transcript.php?sessionid=$sessionid&sid=$sid\">
  	<input type=\"submit\" value=\"Transcript\">
  	</form>
  	
	<form method=\"post\" action=\"welcomepage.php?sessionid=$sessionid\">
  	<input type=\"submit\" value=\"Go Back\">
  	</form>
  	");

?>
