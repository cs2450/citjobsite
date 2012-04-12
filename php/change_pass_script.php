<?php
include_once("../inc/connect.php");
include_once("../inc/func.php");
if(!isset($_SESSION['email'])) {
	header("Location:../prompt_login.php");
	exit();
}
// All we want is password validation here
$error = validate_data('foo', 'foo@foo.com', 'foo@foo.com', 'foo', $_POST['password'], $_POST['confirm_password']);

// Verify their old password as well
$email = &$_SESSION['email'];
$pass = mysql_real_escape_string($_POST['old_password']);
if($_SESSION['user_type']=='student') {
	$sql = "SELECT * FROM students WHERE email='$email' AND password=PASSWORD('$pass')";
}
else {
	$sql = "SELECT * FROM employers WHERE email='$email' AND password=PASSWORD('$pass')";
}
$result = mysql_query($sql) or die(mysql_error());
if(mysql_num_rows($result) != 1)
	$error = "* Invalid password";
if($error != '') {
	header("Location:../change_password.php?error=$error");
	exit();
}

// Finally we can go ahead and change their password
$new = mysql_real_escape_string($_POST['password']);
if($_SESSION['user_type']=='student') {
	$sql = "UPDATE students SET password=PASSWORD('$new') WHERE email='$email' AND password=PASSWORD('$pass')";
}
else {
	$sql = "UPDATE employers SET password=PASSWORD('$new') WHERE email='$email' AND password=PASSWORD('$pass')";
}
mysql_query($sql) or die(mysql_error());
header("Location:../change_password.php?success=true");
exit();
?>
