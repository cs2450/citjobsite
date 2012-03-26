<?php
include_once("../inc/connect.php");

session_start();

// This script is for employer job posting only
if ( !(isset($_SESSION['email'])) || $_SESSION['user_type'] != 'employer') {
	header("Location:../index.php");
	exit();
}
// GOING TO FIX THIS LATER SO IT DOESNT USE GET (not sure what I was thinking)
// Also make sure theres no funny business, allow only what we want in GET
if ( !( isset($_GET['edit']) || isset($_GET['create_job']) ) ) {
	header("Location:../index.php");
	exit();
}
// Finally, make sure a job with id $_GET['edit'] actually exists and we own it
if (isset($_GET['edit'])){
	$job_id = $_GET['edit'];
	$email = $_SESSION['email'];
	$sql = "SELECT id FROM jobs WHERE id='$job_id' AND contact_email='$email'";
	$result = mysql_query($sql);
	if (!mysql_num_rows($result)){
		header("Location:../index.php");
		exit();
	}
}


foreach ($_POST as $key => $value) {
	echo $key.": ".$value."<br/>";
}

// Take care of the job information first so we can ignore it in the loop.
$title = mysql_real_escape_string($_POST['title']);
$wage = mysql_real_escape_string($_POST['wage']);
$desc = str_replace("\n","<br>",$_POST['job_description']);
$desc = mysql_real_escape_string($desc);
$date = date('Y-m-d H:i:s');
$expires = date('Y-m-d', strtotime('+6 month'));
$email = $_SESSION['email'];
$company = $_SESSION['company'];
$name = $_SESSION['name'];
$hours = $_POST['hours'];
$phone = $_SESSION['phone'];

// Check if we are creating or updating
if ($_GET['create_job']) {
	$sql = "INSERT INTO jobs (date,company,contact,job_description,title,wage,hours,expire_date,contact_email,phone,status) VALUES('$date', '$company', '$name', '$desc', '$title', '$wage', '$hours', '$expires', '$email', '$phone', 'active')";
	mysql_query($sql) or die(mysql_error());
	$job_id = mysql_insert_id();
}
else {
	$job_id = $_GET['edit'];
	$sql= "UPDATE jobs SET date='$date', title='$title', wage='$wage', hours='$hours', job_description='$desc' WHERE id='$job_id' AND contact_email='$email'";
	mysql_query($sql) or die(mysql_error());
}


// Get the number of skills in the database
$sql = "SELECT skill_id FROM skills";
$result = mysql_query($sql) or die("Cannot query database: " . mysql_error());

$number_of_skills = mysql_num_rows($result);

// grab our current skills (if any) to avoid duplicate db entries
if ($_SESSION['user_type'] == 'employer') {
	$email = $_SESSION['email'];
	// I couldnt think of a good way to handle skill removal so we remove them all and re-add from the form
	$sql = "DELETE FROM job_skills WHERE job_id='$job_id'";
	mysql_query($sql) or die('failed');
}

// This script assumes that post variables always come in the same order (do they??)
// The flow for adding the skills grabs the match selector, stores in a variable,
// then catches the skill the next iteration and then adds it along with the skill.
// Apologies for this being so messy.
$match = 0;
foreach($_POST as $key => $value)
{
	$do_sql = false;
	// Dont input my hidden value
	if ( $key == 'other_count' ) { continue; }
	
	// Checks for match selector keys
	if (strpos($key,"match") !== false) {
		$match = $value;
		continue;
	}
	
	// Only skill keys are numeric
	else if(is_numeric($key)) {
		$sql = " INSERT INTO job_skills (skill_id,job_id,match_priority) VALUES('$key', '$job_id', '$match');";
		$do_sql = true;
	}
	
	// We are an 'other skill'
	else if(strpos($key,"other") !== false)
	{
		// Avoid adding blank "other skill" boxes
		if ($value == ''){ continue; }
		$value = mysql_real_escape_string($value);
		$sql = " INSERT INTO job_skills (skill_id,other_skill,job_id,match_priority) VALUES('0', '$value', '$job_id', '0');";
		$do_sql = true;
	}
	if ($do_sql) {
		mysql_query($sql) or die("Cannot query database: " . mysql_error());
		$match = 0;
	}
}
if ($_GET['create_job']) {
	header('Location:../jobdetail.php?create_job=true');
}
else {
	header("Location:../jobdetail.php");
}
?>
