<?
include "utility_functions.php";

$pagetype = 's';
$sessionid =$_GET["sessionid"];
verify_session($sessionid, $pagetype);
//sets current schedule
$sql = 	"select title, class.cid, sectionid, semester, credits, enrollflag, student.sid". 
		" from studentenrolledcourses join class on studentenrolledcourses.cid = class.cid". 
		" join student on student.sid = studentenrolledcourses.sid".
		" join clientsession on student.clientid = clientsession.clientid where sessionid = '$sessionid' and enrollflag = 1";

//echo($sql);

$result_array = execute_sql_in_oracle ($sql);
$result = $result_array["flag"];
$cursor = $result_array["cursor"];

if ($result == false){
  display_oracle_error_message($cursor);
  die("Client Query Failed.");
}

echo ("
	<form method=\"post\" action=\"student.php?sessionid=$sessionid\">
  	<input type=\"submit\" value=\"Go Back\">
  	</form>

  	<form method=\"post\" action=\"enroll_multi.php?sessionid=$sessionid\">
  	<input type=\"submit\" value=\"Enroll Multi\"> <br>
  	</form>
  	");

echo("Current Schedule");

echo "<table border=1>";
echo "<tr> <th>Title</th> <th>ClassID</th> <th>Section</th> <th>Semester</th> <th>credits</th> </tr>";


while ($values = oci_fetch_array($cursor)){
	$title = $values[0];
	$cid = $values[1];
	$sectionid = $values[2];
	$semseter = $values[3];
	$credits = $values[4];
	$sid = $values[6];

	echo("<tr>" .
		"<td>$title</td> <td>$cid</td> <td>$sectionid</td> <td>$semseter</td> <td>$credits</td>" .
		" <td> <A HREF=\"enroll_drop_class.php?sessionid=$sessionid&cid=$cid&sid=$sid\">Drop Class</A> </td> ".
		"</tr>");

}


oci_free_statement($cursor);
//free
//search classes by id
echo "</table>";
$currentdate = getdate();
$year = $currentdate['year'];
$month = $currentdate['mon'];
$day = $currentdate['mday'];

echo "Class Search  -- Current Date : ";
echo ($year);
echo "-";
echo ($month);
echo "-";
echo ($day);
echo "<br>";


echo("
	<form method=\"post\" action=\"enroll.php?sessionid=$sessionid\">
	Class ID: <input type=\"text\" size=\"6\" maxlength=\"6\" name=\"cid\"> 
	Section ID: <input type=\"text\" size=\"6\" maxlength=\"6\" name=\"sectionid\"> 
	<input type=\"submit\" value=\"Search\">
	</form>
	");

$cid = $_POST["cid"];
$sectionid = $_POST["sectionid"];

$whereClause = " semester = 2015 ";

if(isset($cid) and trim($cid)!= ""){
	$whereClause .= " and cid like '%$cid%'";
}

if(isset($sectionid) and trim($sectionid)!= ""){
	$whereClause .= " and sectionid like '%$sectionid%'";
}

$sql = "select cid, title, credits, semester, sectionid, stime, maxstudents, numstudents, enrolldeadline from section natural join class where $whereClause";
//echo ($sql);

$result_array = execute_sql_in_oracle ($sql);
$result = $result_array["flag"];
$cursor = $result_array["cursor"];

if ($result == false){
  display_oracle_error_message($cursor);
  die("Client Query Failed.");
}

// Display the query results
echo "<table border=1>";
echo "<tr><th>Class id</th> <th>Title</th> <th>section</th> <th>semester</th> <th>time</th> <th>credits</th> <th>max seats</th> <th>seats left</th> <th>enroll deadline</th></tr>";

// Fetch the result from the cursor one by one
while ($values = oci_fetch_array ($cursor)){
  $cid = $values[0];
  $title = $values[1];
  $credits = $values[2];
  $semester = $values[3];
  $sectionid = $values[4];
  $stime = $values[5];
  $maxstudents = $values[6];
  $numstudents = $values[7];
  $enrolldeadline = $values[8];

  $numstudents = ($maxstudents - $numstudents);

  echo("<tr><td>$cid</td> <td>$title</td> <td>$sectionid</td> <td>$semester</td> <td>$stime</td> <td>$credits</td> <td>$maxstudents</td> <td>$numstudents</td> <td> $enrolldeadline</td>".
  	//" <td> <A HREF=\"enroll_add_class_action.php?sessionid=$sessionid&cid=$cid&sectionid=$sectionid&semester=$semester\">Add Class</A> </td> ".
    "</tr>");
}
//echo("test");
//echo($sid);
oci_free_statement($cursor);

echo "</table>";
//free
?>