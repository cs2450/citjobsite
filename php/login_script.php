<?php
	// This script takes the actions from the login box
	// and redirects accordingly.
	session_start();
	require_once("../inc/connect.php");
	include_once("../inc/func.php");
	if ($_POST['register']) {
		header("Location:../register.php?email=".urlencode($_POST['email']));
		exit();
	}
	else if ($_POST['logout']){
		session_destroy();
		header("Location:../index.php?page=home");
		exit();
	}
	else if ($_POST['All_Jobs']){
		header("Location:../index.php?page=Jobs&pagenumber=1");
		exit();
	}
	else if ($_POST['Matches']){
		header("Location:../index.php?page=Matches&pagenumber=1");
		exit();
	}
	else if ($_POST['Post_Job']){
		header("Location:../post_job.php?page=Post%20Job&action=create");
		exit();
	}
	else if ($_POST['home']){
		header("Location:../profile.php?page=home");
		exit();
	}
	else if ($_POST['index']){
		header("Location:../index.php?page=home");
		exit();
	}
	else if ($_POST['login']){
		$email = mysql_real_escape_string($_POST['email']);
		$pass = mysql_real_escape_string($_POST['password']);
		if (validate($email,$pass,'employer',$error) || 
			validate($email,$pass,'student')) {

			if($_SESSION['user_type'] == 'admin')
			{
				header("Location:../index.php");
				exit();
			}
			
			else {
				header("Location:../profile.php?page=home");
				exit();
			}
		}
		else if($error != NULL) {
			header("Location:../prompt_login.php?error=$error");
			exit();
		}
		else {
			header("Location:../prompt_login.php?error=bad_info");
			exit();
		}
		exit();
	}
?>
