<?php
include_once("../inc/connect.php");
include_once("../inc/func.php");

// This page will handle two operations in a big if-elseif
// First, it takes an email via post and generates a reset password link
// then emails it to the user

// Second, it will accept that link, generate a new password
// and email it to the user. They can then login with it.


// Handle the POST request
if(isset($_POST['email'])) {
	//Just verify valid emails first
	$error = validate_data('foo',$_POST['email'], $_POST['confirm_email'],'foo','foobar','foobar');
	if($error != '') {
		header("Location:../forgotten_password.php?error=$error");
		exit();
	}
	$email = mysql_real_escape_string($_POST['email']);
	
	// Make sure the email is in our DB
	// We have to check both employers AND students :(
	$sql = "SELECT * FROM employers WHERE email='$email'";
	$result = mysql_query($sql) or die(mysql_error());
	$table = 'employers';
	if(mysql_num_rows($result) != 1) {
		$sql = "SELECT * FROM students WHERE email='$email'";
		$result = mysql_query($sql) or die(mysql_error());
		$table = 'students';
		if(mysql_num_rows($result) != 1) {
			// GET LOST!
			header("Location:../forgotten_password.php?error=invalid");
			exit();
		}
	}

	// Getting here means all is well lets generate the recovery key and store it
	$recovery_key = mysql_real_escape_string(gen_random_string(40));
	$sql = "UPDATE $table SET recovery_key='$recovery_key' WHERE email='$email'";
	$result = mysql_query($sql) or die(mysql_error());

	$url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];
	$url = str_replace("/php/reset_pass_script.php","",$url);
	// Now we email them the link that will actually reset their password
	$subject = "Dixie CIT Job Board account recovery request";
	$message = "This email is in response to a recent account recovery request for your account at $url\r\n";
	$message .="Click this link to recover your account: $url/php/reset_pass_script.php?key=$recovery_key\r\n";
	$message .="If you feel you have recieved this message in error, you may disregard it and no changes will be made to your account.";

	mail($email,$subject,$message);
	header("Location:../forgotten_password.php?check_email=true");
	exit();
}
elseif(isset($_GET['key']) && $_GET['key'] != NULL && $_GET['key'] != 'NULL' && $_GET['key'] != '') {
	// Here we verify the recovery key and generate a random pass and email it back
	// Again, we must check both tables :'(
	$key = mysql_real_escape_string($_GET['key']);
	$sql = "SELECT * FROM employers WHERE recovery_key='$key'";
	$result = mysql_query($sql) or die(mysql_error());
	$table = 'employers';
	if(mysql_num_rows($result) != 1) {
		$sql = "SELECT * FROM students WHERE recovery_key='$key'";
		$result = mysql_query($sql) or die(mysql_error());
		$table = 'students';
		if(mysql_num_rows($result) != 1) {
			// GET LOST!
			header("Location:../forgotten_password.php?error=bad_key");
			exit();
		}
	}
	$row = mysql_fetch_assoc($result);
	$email = &$row['email'];

	// We may proceed and generate a random password
	$pass = mysql_real_escape_string(gen_random_string(12));
	$sql = "UPDATE $table SET password=PASSWORD('$pass') WHERE recovery_key='$key' AND email='$email'";
	$result = mysql_query($sql) or die(mysql_error());

	$url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];
	$url = str_replace("/php/reset_pass_script.php","",$url);
	// Now inform the user
	$subject = "Dixie CIT Job Board account recovery successful";
	$message = "This email is in response to a recent account recovery request for your account at $url\r\n";
	$message .="Your account has been recovered and you have been assigned a randomly generated password which you may now use to login.\r\n";
	$message .="Please remember to change your password once you have logged in.\r\n";
	$message .="New password: $pass";

	mail($email,$subject,$message);

	// Nullify the recovery key so it can't be used again
	$sql = "UPDATE $table SET recovery_key=NULL WHERE email='$email'";
	$result = mysql_query($sql) or die(mysql_error());

	header("Location:../forgotten_password.php?success=true");
	exit();
}
else {
	// Go away
	header("Location:../index.php");
	exit();
}
?>
