<?php
include_once("../inc/connect.php");

session_start();

$student_id = $_SESSION['student_id'];

// Get the number of skills in the database
$sql = "SELECT skill_id FROM skills";
$result = mysql_query($sql) or die("Cannot query database: " . mysql_error());

$number_of_skills = mysql_num_rows($result);

foreach($_POST as $key => $value)
{
	if(is_numeric($key))
		$sql = "INSERT INTO student_skills (student_id,skill_id) VALUES('$student_id', '$key')";
	
	else
	{
		$value = mysql_real_escape_string($value);
		$sql = "INSERT INTO student_skills (student_id,other_skill) VALUES('$student_id', '$value')";
	}
		
	mysql_query($sql) or die("Cannot query database: " . mysql_error());
}

header('Location:../resume.php');
?>