<?php
include_once("../inc/connect.php");

session_start();

// This script is for student skills only
if (!(isset($_SESSION['email']) || $_SESSION['user_type'] != 'student')) {
	header("Location:../index.php");
	exit();
}

$student_id = $_SESSION['student_id'];

// Get the number of skills in the database
$sql = "SELECT skill_id FROM skills";
$result = mysql_query($sql) or die("Cannot query database: " . mysql_error());

$number_of_skills = mysql_num_rows($result);

// If we are a student, grab our current skills (if any) to avoid duplicate db entries
if ($_SESSION['user_type'] == 'student') {
	$id = $_SESSION['student_id'];
	// I couldnt think of a good way to handle skill removal so we remove them and re-add from the form
	$sql = "DELETE FROM student_skills WHERE student_id='".$id."'";
	mysql_query($sql) or die('failed');
}



foreach($_POST as $key => $value)
{
	// Dont input my hidden value
	if ( $key == 'other_count' ) { continue; }

	if(is_numeric($key))
		$sql = " INSERT INTO student_skills (student_id,skill_id) VALUES('$student_id', '$key');";
	
	else
	{
		// Avoid adding blank "other skill" boxes
		if ($value == ''){ continue; }
		$value = mysql_real_escape_string($value);
		$sql = " INSERT INTO student_skills (student_id,other_skill) VALUES('$student_id', '$value');";
	}
	mysql_query($sql) or die("Cannot query database: " . mysql_error());
}
if ($_GET['student_register']) {
	header('Location:../resume.php?student_register=true');
}
else {
	header("Location:../profile.php");
}
?>
