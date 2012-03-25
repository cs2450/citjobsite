<?php
include_once("../inc/connect.php");

session_start();

$student_id = $_SESSION['student_id'];

// Get the number of skills in the database
$sql = "SELECT skill_id FROM skills";
$result = mysql_query($sql) or die("Cannot query database: " . mysql_error());

$number_of_skills = mysql_num_rows($result);

// If we are a student, grab our current skills (if any) to avoid duplicate db entries
if ($_SESSION['user_type'] == 'student') {
	$id = $_SESSION['student_id'];
	$sql = "SELECT skill_id, other_skill FROM student_skills WHERE student_id='".$id."'";
	$result= mysql_query($sql) or die("failed fetching skills");
	$current_skills = array();
	while ($row=mysql_fetch_array($result)) {
		$current_skills[$row['skill_id']] = true;
	}
}
// I couldnt think of a good way to handle the "other skills" so we remove them and re-add from the form
$sql = "DELETE FROM student_skills WHERE student_id='$student_id' and skill_id='0'";
mysql_query($sql) or die('failed');

foreach($_POST as $key => $value)
{
	// Dont input my hidden value, or any skills they already have
	if ( $key == 'other_count' || $current_skills[$key] ) { continue; }

	if(is_numeric($key))
		$sql = " INSERT INTO student_skills (student_id,skill_id) VALUES('$student_id', '$key');";
	
	else
	{
		$value = mysql_real_escape_string($value);
		$sql = " INSERT INTO student_skills (student_id,other_skill) VALUES('$student_id', '$value');";
	}
	mysql_query($sql) or die("Cannot query database: " . mysql_error());
}
if ($_GET['student_register']) {
	header('Location:../resume.php');
}
else {
	header("Location:../profile.php");
}
?>
