<?

	$sqlserver = "mysql10.000webhost.com";
	$sqluser = "a3369228_user";
	$sqldatabase = "a3369228_jobs";
	$slqdatabase2= "DB2";
	$password="cs2450jobs";

  mysql_connect($sqlserver,$sqluser,$password);
  @mysql_select_db($sqldatabase) or die( "Unable to select database");

?>