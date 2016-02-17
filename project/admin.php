<?
include "utility_functions.php";

$pagetype = 'a';
$sessionid =$_GET["sessionid"];
verify_session($sessionid, $pagetype);


// Generate the query section
echo("
  <form method=\"post\" action=\"admin.php?sessionid=$sessionid\">
  SIDNumber: <input type=\"text\" size=\"5\" maxlength=\"10\" name=\"sid\"> 
  FirstName: <input type=\"text\" size=\"20\" maxlength=\"30\" name=\"fname\"> 
  LastName: <input type=\"text\" size=\"20\" maxlength=\"30\" name=\"lname\">
  ClientID: <input type=\"text\" size=\"20\" maxlength=\"30\" name=\"clientid\">
  <input type=\"submit\" value=\"Search\">
  </form>

  <form method=\"post\" action=\"welcomepage.php?sessionid=$sessionid\">
  <input type=\"submit\" value=\"Go Back\">
  </form>

  <form method=\"post\" action=\"student_add.php?sessionid=$sessionid\">
  <input type=\"submit\" value=\"Add a new student\">
  </form>
  ");


// Interpret the query requirements
$sid = $_POST["sid"];
$fname = $_POST["fname"];
$lname = $_POST["lname"];
$clientid = $_POST["clientid"];

$whereClause = " 1=1 ";

if (isset($sid) and trim($sid)!= "") { 
  $whereClause .= " and sid = '$sid'"; 
}

if (isset($fname) and $fname!= "") { 
  $whereClause .= " and fname like '%$fname%'"; 
}

if (isset($lname) and $lname!= "") { 
  $whereClause .= " and lname like '%$lname%'"; 
}

if (isset($clientid) and trim($clientid)!= "") { 
  $whereClause .= " and clientid = '$clientid'"; 
}


// Form the query and execute it
$sql = "select sid, fname, lname, clientid, password, aflag, sflag from student natural join myclient where $whereClause";
//echo($sql);

$result_array = execute_sql_in_oracle ($sql);
$result = $result_array["flag"];
$cursor = $result_array["cursor"];

if ($result == false){
  display_oracle_error_message($cursor);
  die("Client Query Failed.");
}


// Display the query results
echo "<table border=1>";
echo "<tr> <th>SIDNumber</th> <th>First Name</th> <th>Last Name</th> <th>ClientID</th> <th>password</th> <th>Admin?</th> <th>Student?</th> <th>Update</th> <th>Delete</th></tr>";

// Fetch the result from the cursor one by one
while ($values = oci_fetch_array ($cursor)){
  $sid = $values[0];
  $fname = $values[1];
  $lname = $values[2];
  $clientid = $values[3];
  $password = $values[4];
  $aflag = $values[5];
  $sflag = $values[6];

  echo("<tr>" . 
    "<td>$sid</td> <td>$fname</td> <td>$lname</td> ".
    "<td>$clientid</td> <td>$password</td> <td>$aflag</td> <td>$sflag</td> ".
    " <td> <A HREF=\"student_update.php?sessionid=$sessionid&clientid=$clientid\">Update</A> </td> ".
    " <td> <A HREF=\"student_delete.php?sessionid=$sessionid&clientid=$clientid\">Delete</A> </td> ".
    "</tr>");
}

oci_free_statement($cursor);

echo "</table>";
?>