<?
ini_set( "display_errors", 0);  

include "utility_functions.php";
$connection = oci_connect ("gq039", "cxbnow", "gqiannew2:1521/pdborcl");
if ($connection == false){
   echo oci_error()."<BR>";
   exit;
}

$pagetype = 'a';
$sessionid =$_GET["sessionid"];
verify_session($sessionid, $pagetype);


// $dnumber = trim($_POST["dnumber"]);
// if ($dnumber == "") $dnumber = 'NULL';
$clientid = $_POST["clientid"];
$password = $_POST["password"];
$aflag = $_POST["aflag"];
$sflag = $_POST["sflag"];

// the sql string
$sql = "insert into myclient values ('$clientid', '$password', '$aflag', '$sflag')";
//echo($sql);

$result_array = execute_sql_in_oracle ($sql);
$result = $result_array["flag"];
$cursor = $result_array["cursor"];

if ($result == false){
  echo "<B>Insertion Failed.</B> <BR />";

  display_oracle_error_message($cursor);
  
  die("<i> 

  <form method=\"post\" action=\"student_add.php?sessionid=$sessionid\">

  <input type=\"hidden\" value = \"$clientid\" name=\"clientid\">
  <input type=\"hidden\" value = \"$password\" name=\"password\">
  <input type=\"hidden\" value = \"$aflag\" name=\"aflag\">
  <input type=\"hidden\" value = \"$sflag\" name=\"sflag\">

  
  Read the error message, and then try again:
  <input type=\"submit\" value=\"Go Back\">
  </form>

  </i>
  ");
}

$fname = $_POST["fname"];
$lname = $_POST["lname"];
$sid;
$error;

$sql = "begin new_student_id(:fname, :lname, :clientid, :sid, :error); end;";
//echo($sql);
$cursor = oci_parse($connection, $sql);
if($cursor == false){
  echo oci_error($connection)."<br>";
  exit;
}
oci_bind_by_name($cursor, ":sid", &$sid, 8);
oci_bind_by_name($cursor, ":fname", &$fname, 30);
oci_bind_by_name($cursor, ":lname", &$lname, 30);
oci_bind_by_name($cursor, ":clientid", &$clientid, 30);
oci_bind_by_name($cursor, ":error", &$error, 100);

$result = oci_execute($cursor, OCI_NO_AUTO_COMMIT);
if($result == false){
  display_oracle_error_message($cursor);
  echo oci_error($cursor)."<BR>";
  exit;
}
oci_free_statement($cursor);
echo($error);


$age = $_POST["age"];
$streetnumber = $_POST["streetnumber"];
$streetname = $_POST["streetname"];
$city = $_POST["city"];
$state = $_POST["state"];
$zipcode = $_POST["zipcode"];
$type = $_POST["type"];

$sql = "update student set age = $age, streetnumber = '$streetnumber', streetname = '$streetname', ".
      "city = '$city', state = '$state', zipCode = $zipcode, typeflag = '$type' ".
      "where sid = '$sid'";
echo($sql);

$result_array = execute_sql_in_oracle ($sql);
$result = $result_array["flag"];
$cursor = $result_array["cursor"];

if ($result == false){
  display_oracle_error_message($cursor);
  die("Client Query Failed.");
}


//Header("Location:admin.php?sessionid=$sessionid");
?>