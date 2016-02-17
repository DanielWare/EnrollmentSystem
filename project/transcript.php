<?
include "utility_functions.php";

$pagetype = 's';
$sessionid =$_GET["sessionid"];
$sid = $_GET["sid"];
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

$clientid = $_GET["clientid"];
$sql = "select title, class.cid, sectionid, semester, credits, grade, enrollflag, gpa from studentenrolledcourses join class on studentenrolledcourses.cid = class.cid". 
		" join student on student.sid = studentenrolledcourses.sid".
		" join clientsession on student.clientid = clientsession.clientid where sessionid = '$sessionid' order by semester";
//echo ($sql);

$result_array = execute_sql_in_oracle ($sql);
$result = $result_array["flag"];
$cursor = $result_array["cursor"];

if ($result == false){
  display_oracle_error_message($cursor);
  die("Client Query Failed.");
}


echo "<table border=1>";
echo "<tr> <th>Title</th> <th>ClassID</th> <th>Section</th> <th>Semester</th> <th>credits</th> <th>grade</th> <th>currently enrolled</th> </tr>";


while ($values = oci_fetch_array($cursor)){
	$title = $values[0];
	$classid = $values[1];
	$sectionid = $values[2];
	$semseter = $values[3];
	$credits = $values[4];
	$grade = $values[5];
	$enrolled = $values[6];
	$gpa = $values[7];

	if($grade > 3){
		$grade = 'A';
	}else if($grade > 2){
		$grade = 'B';
	}else if($grade > 1){
		$grade = 'C';
	}else if ($grade > 0){
		$grade = 'D';
	}else{
		$grade = 'F';
	}
	if($enrolled == 1){
		$grade = '';
	}

	echo("<tr>" .
		"<td>$title</td> <td>$classid</td> <td>$sectionid</td> <td>$semseter</td> <td>$credits</td> <td>$grade</td> <td>$enrolled</td>" .
		"</tr>");
}

oci_free_statement($cursor);


echo ("<tr>" .
	"<td>-</td> <td>-</td> <td>-</td> <td>-</td> <td>Total Credits</td> <td>GPA</td> <td>-</td>" .
	"</tr>");



$sql = "select SUM(credits) from class natural join studentenrolledcourses where sid = '$sid' and enrollflag = 0";
//echo($sql);

$result_array = execute_sql_in_oracle ($sql);
$result = $result_array["flag"];
$cursor = $result_array["cursor"];

if ($result == false){
  display_oracle_error_message($cursor);
  die("Client Query Failed.");
}
$values = oci_fetch_array($cursor);
$total_credits = $values[0];

oci_free_statement($cursor);


echo ("<tr>" .
	"<td>-</td> <td>-</td> <td>-</td> <td>-</td> <td>$total_credits</td> <td>$gpa</td> <td>-</td>" .
	"</tr>");

echo "</table> <br>";

echo ("
	<form method=\"post\" action=\"student.php?sessionid=$sessionid\">
  	<input type=\"submit\" value=\"Go Back\">
  	</form>
  	");

?>