<?

	$sqlserver = "mysql.cs.dixie.edu";
	$sqluser = "ncox";
	$sqldatabase = "ncox";
	$slqdatabase2= "ncox";
	$password="iwish4utodie";

  mysql_connect($sqlserver,$sqluser,$password);
  @mysql_select_db($sqldatabase) or die( "Unable to select database");

?>
