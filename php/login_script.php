<?php
	// This script takes the actions from the login box
	// and redirects accordingly.
	session_start();
	require_once("../inc/connect.php");
	include_once("../inc/func.php");

	if ($_POST['register']) {
		header("Location:../register.php");
		exit();
	}
	else if ($_POST['logout']){
		session_destroy();
		header("Location:../index.php");
		exit();
	}
	else if ($_POST['matches']){
		//do something
		exit();
	}
	else if ($_POST['home']){
		header("Location:../profile.php");
		exit();
	}
	else if ($_POST['login']){
		if (validate($_POST['email'],$_POST['password'],'employer')) {
			header("Location:../profile.php");
			exit();
		}
		else if (validate($_POST['email'],$_POST['password'],'student')) {
			header("Location:../profile.php");
			exit();
		}
		else {
			header("Location:../prompt_login.php");
			exit();
		}
		exit();
	}
?>
