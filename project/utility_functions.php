<?
// Contains commonly used functions.


//********************
// Run the sql, and return the error flag and the cursor in an array
// The array index "flag" contains the flag.
// The array index "cursor" contains the cursor.
//********************
function execute_sql_in_oracle($sql) {
  //putenv("ORACLE_HOME=/home/oracle/OraHome1");
  //putenv("ORACLE_SID=orcl");

  $connection = oci_connect ("gq039", "cxbnow", "gqiannew2:1521/pdborcl");
  if($connection == false){
    // failed to connect
    display_oracle_error_message(null);
    die("Failed to connect");
  }

  $cursor = oci_parse($connection, $sql);

  if ($cursor == false) {
    display_oracle_error_message($connection);
    oci_close ($connection);
    // sql failed 
    die("SQL Parsing Failed");
  }

  $result = oci_execute($cursor);

  if ($result == false) {
    display_oracle_error_message($cursor);
    oci_close ($connection);
    // sql failed 
    die("SQL execution Failed");
  }

  // commit the result
  //oci_commit ($connection);

  // close the connection with oracle
  oci_close ($connection);  

  $return_array["flag"] = $result;
  $return_array["cursor"] = $cursor;

  return $return_array;
}

//********************
// Verify the session id.  
// Return normally if it is verified.
// Terminate the script otherwise.
//********************
function verify_session($sessionid, $pagetype) {
  // lookup the sessionid in the session table to ascertain the clientid 
  $sql = "select clientid " .
       "from clientsession " .
       "where sessionid='$sessionid'";  

  $result_array = execute_sql_in_oracle ($sql);
  $result = $result_array["flag"];
  $cursor = $result_array["cursor"];

  $result = oci_execute($cursor);
  if ($result == false){
    display_oracle_error_message($cursor);
    die("SQL Execution problem.");
  }
  if(!($values = oci_fetch_array ($cursor))){
    // no active session - clientid is unknown
    die("Invalid client!");
  } 
  //oci_free_statement($cursor);


  $sql = "select aflag, sflag " .
       "from myclient natural join clientsession " .
       "where sessionid='$sessionid'";
  $result_array = execute_sql_in_oracle ($sql);
  $result = $result_array["flag"];
  $cursor = $result_array["cursor"];

  $result = oci_execute($cursor);
  if ($result == false){
    display_oracle_error_message($cursor);
    die("SQL Execution problem.");
  }
  else{
    if($values = oci_fetch_array ($cursor)){
      // found the sessionid
      if($pagetype == 'a'){
        if($values[0] != 1){
          die("Invalid Client! Click <A HREF = \"welcomepage.php?sessionid=$sessionid\">here</A> to go back.");
        }
      } else if ($pagetype == 's'){
        if($values[1] != 1){
          die("Invalid Client! Click <A HREF = \"welcomepage.php?sessionid=$sessionid\">here</A> to go back.");
        }
      }
    }
  }
  oci_free_statement($cursor);
} 

//********************
// Takes an executed errored oracle cursor as input.
// Display an initerpreted error message.
//********************
function display_oracle_error_message($resource) {
  if (is_null($resource))
    $err = oci_error();
  else
    $err = oci_error($resource);

  echo "<BR />";
  echo "Oracle Error Code: " . $err['code'] . "<BR />";
  echo "Oracle Error Message: " . $err['message'] . "<BR />" . "<BR />";
  
  if ($err['code'] == 1)
    echo("Duplicate Values.  <BR /><BR />");
  else if ($err['code'] == 984 or $err['code'] == 1861 
    or $err['code'] == 1830 or $err['code'] == 1839 or $err['code'] == 1847
    or $err['code'] == 1858 or $err['code'] == 1841)
    echo("Wrong type of value entered.  <BR /><BR />");
  else if ($err['code'] == 1400 or $err['code'] == 1407)
    echo("Required field not correctly filled.  <BR /><BR />");
  else if ($err['code'] == 2292)
    echo("Child records exist.  Need to delete or update them first.  <BR /><BR />");
}
?>