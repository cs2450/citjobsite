<?php
include_once("../inc/connect.php");
include_once("../inc/func.php");

session_start();

$error = validate_data($_POST['name'], $_POST['register_email'], $_POST['confirm_email'], $_POST['phone'], $_POST['register_password'], $_POST['confirm_password']);

if($error != ''){
	header("Location:../register.php?error=".$error);
	exit();
}

// If no errors, go ahead and finish registering the individual
$_SESSION['user_type'] = $user = $_POST['register_type'];

$name = mysql_real_escape_string($_POST['name']);
$email = mysql_real_escape_string($_POST['register_email']);
$phone = mysql_real_escape_string($_POST['phone']);
$pass = mysql_real_escape_string($_POST['register_password']);
$company = isset($_POST['company']) ? mysql_real_escape_string($_POST['company']) : NULL;
$description = isset($_POST['description']) ? mysql_real_escape_string($_POST['description']) : NULL;

// Check if email is already in employers and kick them out if so.
$sql = "SELECT * FROM employers WHERE email='$email'";
$result = mysql_query($sql) or die(mysql_error());
if (mysql_num_rows($result) > 0){
	header("Location:../register.php?error=user_exists");
	exit();
}

// Check if email is already in students and kick them out if so.
$sql = "SELECT * FROM students WHERE email='$email'";
$result = mysql_query($sql) or die(mysql_error());
if (mysql_num_rows($result) > 0){
	header("Location:../register.php?error=user_exists");
	exit();
}

if($user == 'employer')
{
	$sql = "INSERT INTO employers (email, password, name, company, description, phone, access) VALUES('$email', PASSWORD('$pass'), '$name', '$company', '$description', '$phone', '1');";
	
    mysql_query($sql) or die(mysql_error());
    
	if(validate($email, $pass, $user, $error)){
		header("Location:../profile.php?page=home&employer_register=true");
		exit();
	}
}

// if the user_type is a student
else if($user == 'student')
{
	$sql = "INSERT INTO students (name, email, phone, password) VALUES('$name', '$email', '$phone', PASSWORD('$pass'));";
	
    mysql_query($sql) or die(mysql_error());
	if(validate($email, $pass, $user, $error)){
		header("Location: ../skills.php?page=home&student_register=true");
		exit();
	}
}

else
{
	echo 'Invalid User Type';
}
?>
