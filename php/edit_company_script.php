<?php
	chdir('..');
	require_once("inc/connect.php");
	require_once("inc/func.php");
	session_start();

	if(isset($_FILES['img_upload']) && !empty($_FILES['img_upload']['name']))
	{
		handleFileUpload($_FILES['img_upload'], 'logo');
	}
	
	$desc = mysql_real_escape_string($_POST['description']);
	$sql = "UPDATE employers SET description='$desc' WHERE email='".$_SESSION['email']."'";
	mysql_query($sql) or die(mysql_error());
	header("Location:../profile.php");
?>
