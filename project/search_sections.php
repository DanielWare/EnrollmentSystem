<?
include "utility_functions.php";

$sessionid =$_GET["sessionid"];
verify_session($sessionid);

echo("
  <form method=\"post\" action=\"search_sections.php?sessionid=$sessionid\">
  Class ID: <input type=\"text\" size=\"6\" maxlength=\"6\" name=\"cid\"> 
  Section ID: <input type=\"text\" size=\"6\" maxlength=\"6\" name=\"sectionid\"> 
  <input type=\"submit\" value=\"Search\">
  </form>

  <form method=\"post\" action=\"welcomepage.php?sessionid=$sessionid\">
  <input type=\"submit\" value=\"Go Back\">
  </form>
  ");

$cid = $_POST["cid"];
$sectionid = $_POST["sectionid"];

$whereClause = " 1=1 ";

if(isset($cid) and trim($cid)!= ""){
	$whereClause .= " and cid like '%$cid%'";
}

if(isset($sectionid) and trim($sectionid)!= ""){
	$whereClause .= " and sectionid like '%$sectionid%'";
}

$sql = "select cid, title, credits, semester, sectionid, stime from section natural join class where $whereClause";
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
echo "<tr><th>Class id</th> <th>Title</th> <th>section</th> <th>semester</th> <th>time</th> <th>credits</th> </tr>";

// Fetch the result from the cursor one by one
while ($values = oci_fetch_array ($cursor)){
  $cid = $values[0];
  $title = $values[1];
  $credits = $values[2];
  $semester = $values[3];
  $sectionid = $values[4];
  $stime = $values[5];

  echo("<tr><td>$cid</td> <td>$title</td> <td>$sectionid</td> <td>$semester</td> <td>$stime</td> <td>$credits</td>".
    "</tr>");
}

oci_free_statement($cursor);

echo "</table>";

?>