<?php
include_once("../inc/connect.php");
include_once("../inc/func.php");

session_start();

$error = validate_data($_POST['name'], $_POST['register_email'], $_POST['confirm_email'], $_POST['phone'], $_POST['register_password'], $_POST['confirm_password'], $_POST['company']);

if($error != '');
	header("Location:../register.php?error=".$error);

// If no errors, go ahead and finish registering the individual
$_SESSION['user_type'] = $user = $_POST['register_type'];

$name = mysql_real_escape_string($_POST['name']);
$email = mysql_real_escape_string($_POST['register_email']);
$phone = mysql_real_escape_string($_POST['phone']);
$pass = mysql_real_escape_string($_POST['register_password']);
$company = isset($_POST['company']) ? mysql_real_escape_string($_POST['company']) : NULL;

if($user == 'employer')
{
	$sql = "INSERT INTO employers (email, password, name, company, phone, access) VALUES('$email', '$pass', '$name', '$company', '$phone', '1');";
	
    mysql_query($sql) or die(mysql_error());
    
	if(validate($email, $pass, $user))
		echo 'hi';
}

// if the user_type is a student
else if($user == 'student')
{
	$sql = "INSERT INTO students (name, email, phone, password) VALUES('$name', '$email', '$phone', 	'$pass');";
	
    mysql_query($sql) or die(mysql_error());
    
	if(validate($email, $pass, $user))
		header("Location: ../skills.php");
}

else
{
	echo 'Invalid User Type';
}
?>