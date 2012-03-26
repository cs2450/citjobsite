<?php
	require_once("../inc/connect.php");
	session_start();
	$desc = mysql_real_escape_string($_POST['description']);
	$sql = "UPDATE employers SET description='$desc' WHERE email='".$_SESSION['email']."'";
	mysql_query($sql) or die(mysql_error());
	header("Location:../profile.php");
?>
