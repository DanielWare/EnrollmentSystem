<?
include "utility_functions.php";
$connection = oci_connect ("gq039", "cxbnow", "gqiannew2:1521/pdborcl");
if ($connection == false){
   echo oci_error()."<BR>";
   exit;
}


$pagetype = 's';
$sessionid =$_GET["sessionid"];
$semester = 2015;
verify_session($sessionid, $pagetype);

$cid1=$_POST["cid1"];
$sectionid1=$_POST["sectionid1"];
$cid2=$_POST["cid2"];
$sectionid2=$_POST["sectionid2"];
$cid3=$_POST["cid3"];
$sectionid3=$_POST["sectionid3"];
$cid4=$_POST["cid4"];
$sectionid4=$_POST["sectionid4"];
$cid5=$_POST["cid5"];
$sectionid5=$_POST["sectionid5"];


$sql = "select sid from student natural join clientsession where sessionid = '$sessionid'";
//echo($sql);

$result_array = execute_sql_in_oracle ($sql);
$result = $result_array["flag"];
$cursor = $result_array["cursor"];

if ($result == false){
  display_oracle_error_message($cursor);
  die("Client Query Failed.");
}
$values = oci_fetch_array($cursor);
$sid = $values[0];
oci_free_statement($cursor);

$error;///error string
$final_error;

$cid_string = "cid";
$sectionid_string = "sectionid";
$count = 1;

for($i = 1; $i < 6; $i++){
	$cid=$_POST["$cid_string".$i];
	$sectionid=$_POST["$sectionid_string".$i];


	if(isset($cid) and trim($cid)!= ""){
		if(isset($sectionid) and trim($sectionid)!= ""){

			//check deadline
			$sql = "begin check_deadline(:cid, :sectionid, :semester, :error); end;";
			//echo($sql);
			$cursor = oci_parse($connection, $sql);
			if($cursor == false){
				echo oci_error($connection)."<br>";
				exit;
			}
			oci_bind_by_name($cursor, ":error", &$error, 100);
			oci_bind_by_name($cursor, ":cid", &$cid, 6);
			oci_bind_by_name($cursor, ":sectionid", &$sectionid, 4);
			oci_bind_by_name($cursor, ":semester", &$semester, 4);
			oci_bind_by_name($cursor, ":sid", &$sid, 8);

			$result = oci_execute($cursor, OCI_NO_AUTO_COMMIT);
			if($result == false){
				display_oracle_error_message($cursor);
				echo oci_error($cursor)."<BR>";
				exit;
			}
			oci_free_statement($cursor);
			//end deadline check
			//echo("past deadline");

			//check passed course
			if(isset($error) and trim($error)!=""){//previous errors so do not enroll
				//continue;
			}else{
				$sql = "begin check_passed_course(:cid, :sid, :error); end;";
				//echo($sql);
				$cursor = oci_parse($connection, $sql);
							if($cursor == false){
					echo oci_error($connection)."<br>";
					exit;
				}
				oci_bind_by_name($cursor, ":error", &$error, 100);
				oci_bind_by_name($cursor, ":cid", &$cid, 6);
				oci_bind_by_name($cursor, ":sid", &$sid, 8);

				$result = oci_execute($cursor, OCI_NO_AUTO_COMMIT);
				if($result == false){
					display_oracle_error_message($cursor);
					echo oci_error($cursor)."<BR>";
					exit;
				}
				oci_free_statement($cursor);

			}
			//end passed course check
			//echo("past course");

			//check prereqs taken
			if(isset($error) and trim($error)!=""){//previous errors so do not enroll
				//continue;
			}else{
				$sql = "begin check_prereq(:cid, :sectionid, :sid, :error); end;";
				//echo($sql);
				$cursor = oci_parse($connection, $sql);
				if($cursor == false){
					echo oci_error($connection)."<br>";
					exit;
				}
				oci_bind_by_name($cursor, ":error", &$error, 100);
				oci_bind_by_name($cursor, ":cid", &$cid, 6);
				oci_bind_by_name($cursor, ":sectionid", &$sectionid, 4);
				oci_bind_by_name($cursor, ":sid", &$sid, 8);
				$result = oci_execute($cursor, OCI_NO_AUTO_COMMIT);
				if($result == false){
					display_oracle_error_message($cursor);
					echo oci_error($cursor)."<BR>";
					exit;
				}
				oci_free_statement($cursor);
			}
			//end  prereqs taken check
			//echo("taken prereqs");

			//check for seat and enroll
			if(isset($error) and trim($error)!=""){//previous errors so do not enroll
				//continue;
			}else{//no previous errors so check seat and enroll
				$sql = "begin check_seat_available(:cid, :sectionid, :semester, :sid, :error); end;";
				//echo($sql);
				$cursor = oci_parse($connection, $sql);
				if($cursor == false){
					echo oci_error($connection)."<br>";
					exit;
				}
				oci_bind_by_name($cursor, ":error", &$error, 100);
				oci_bind_by_name($cursor, ":cid", &$cid, 6);
				oci_bind_by_name($cursor, ":sectionid", &$sectionid, 4);
				oci_bind_by_name($cursor, ":semester", &$semester, 4);
				oci_bind_by_name($cursor, ":sid", &$sid, 8);
				$result = oci_execute($cursor, OCI_NO_AUTO_COMMIT);
				if($result == false){
					display_oracle_error_message($cursor);
					echo oci_error($cursor)."<BR>";
					exit;
				}
				oci_free_statement($cursor);
			}
			//end check for seat and enroll
			//echo("has seat and enrolled");
			if(isset($error) and trim($error)!=""){
				//echo($error);
				$final_error .= "<br>" . $error;
				$error = "";
			}
		}//end isset
	}//end isset

}//end for loop

if(isset($final_error) and trim($final_error)!= ""){
	echo("ERROR:");
	echo($final_error);
	die("<br>Click <A HREF = \"enroll.php?sessionid=$sessionid\">here</A> to go back.");
}

oci_close($connection);
Header("Location:enroll.php?sessionid=$sessionid");
?>