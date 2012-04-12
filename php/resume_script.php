<?php
chdir("..");
include_once("inc/connect.php");
include_once("inc/func.php");
session_start();

// If a resume file was uploaded, handle the resume placement and validation

if(isset($_FILES['resume']) && !empty($_FILES['resume']['name']))
{
	handleFileUpload($_FILES['resume'], 'resume');
}

if(isset($_FILES['image']) && !empty($_FILES['image']['name']))
{
	handleFileUpload($_FILES['image'], 'profile');
}


	// An unchecked 'notification' checkbox doesn't seem to show up in post
	// So to allow for toggling it we always set to false then true if needed
	$sql = "UPDATE students SET notification=0,hide_info=0 WHERE email='".$_SESSION['email']."'";
	mysql_query($sql) or die(mysql_error());
	// Also (annoyingly) we must kick them off the mail_list and re-add if needed
	$sql = "DELETE FROM mail_list WHERE email='".$_SESSION['email']."'";
	mysql_query($sql) or die(mysql_error());
	
	// Dump all inputs to db
	foreach ($_POST as $key => $value) {
		echo $key.": ".$value."<br/>";
		$key = mysql_real_escape_string($key);
		$value = mysql_real_escape_string($value);
		$sql = "UPDATE students SET $key='$value' WHERE email='".$_SESSION['email']."'";
		// Add to mail list
		if($key=='notification' && $value='on') {
			$sql = "INSERT INTO mail_list VALUES('".$_SESSION['email']."', '".$_SESSION['name']."')";
			mysql_query($sql) or die(mysql_error());
		}
		if (($key == 'notification' && $value="on") || ($key == 'hide_info' && $value=="on"))
			$sql = "UPDATE students SET $key=1 WHERE email='".$_SESSION['email']."'";
			
		mysql_query($sql) or die(mysql_error());
	}
	
	if ($_GET['student_register'])
		header("Location:../profile.php?page=home&student_register=true");
	else
		header("Location:../profile.php?page=home");

	exit();
?>
