<?php
include_once("../inc/connect.php");
include_once("../inc/func.php");

// You can opt in or out via POST from mailing_list.php
// GET only supports opting out
if(isset($_POST['email'])) {
	$error = validate_data($_POST['name'],$_POST['email'],$_POST['confirm_email'],'foo','foobar','foobar');
	if($error != ''){
		header("Location:../mailing_list.php?error=$error");
		exit();
	}
	$email = mysql_real_escape_string($_POST['email']);
	$name = mysql_real_escape_string($_POST['name']);

	if($_POST['opt'] == 'out') {
		$sql = "DELETE FROM mail_list WHERE email='$email'";
		$action = 'removed.';
	}
	elseif($_POST['opt'] == 'in') {
		$sql = "INSERT IGNORE INTO mail_list VALUES('$email', '$name')";
		$action = 'added.';
	}
	else {
		$error = "* Please select opt-in or opt-out.";
		header("Location:../mailing_list.php?error=$error");
		exit();
	}
	mysql_query($sql) or die(mysql_error());
	header("Location:../mailing_list.php?action=$action");
	exit();
}
// Now handle opt-out via GET
elseif(isset($_GET['email'])) {
	$regexp="/^[a-z0-9]+([_\\.-][a-z0-9]+)*@([a-z0-9]+([\.-][a-z0-9]+)*)+\\.[a-z]{2,}$/i";
	if(!preg_match($regexp, $_GET['email'])) {
		$error = "* Invalid email address";
		header("Location:../mailing_list.php?error=$error");
		exit();
	}
	$email = mysql_real_escape_string($_GET['email']);
	$sql = "DELETE FROM mail_list WHERE email='$email'";
	mysql_query($sql) or die(mysql_error());
	header("Location:../mailing_list.php?action=removed.");
	exit();
}
else {
	header("Location:../mailing_list.php");
	exit();
}
?>
