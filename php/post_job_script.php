<?php
include_once("../inc/connect.php");

session_start();

// This script is for employer job posting only
if ( !(isset($_SESSION['email'])) || $_SESSION['user_type'] != 'employer') {
	header("Location:../index.php");
	exit();
}
$action = $_GET['action'];
// Make sure theres no funny business, allow only what we want in GET
if ($action != 'create' && $action != 'edit' && $action != 'renew' && $action != 'deleted' && $action != 'filled') { 
	header("Location:../index.php");
	exit();
}

// If we are changing a job, make sure it actually exists and we own it
if ($action != 'create') {
	$job_id = $_GET['id'];
	$email = $_SESSION['email'];
	$sql = "SELECT id FROM jobs WHERE id='$job_id' AND contact_email='$email'";
	$result = mysql_query($sql);
	if (!mysql_num_rows($result)){
		header("Location:../index.php");
		exit();
	}

	// Renewing a job is simple. We handle it here
	if ($action == 'renew') {
		$expires  = date('Y-m-d', strtotime(date('Y-m-d') . "+".$_GET['lifetime']." months"));
	
		$sql = "UPDATE jobs SET expire_date='$expires', status='active' WHERE id='$job_id'";
		mysql_query($sql) or die(mysql_error());
		// Also reactivate any skills associated
		$sql = "UPDATE job_skills SET active=1 WHERE job_id='$job_id'";
		mysql_query($sql) or die(mysql_error());
		
		echo date('M d, Y', strtotime($expires));
		exit();
	}
	// So is deleting and marking as filled. We don't actually delete it though.
	else if ($action == 'filled' || $action == 'deleted') {
		$rm = &$_GET['action'];
		$sql = "UPDATE jobs SET status='$rm' WHERE id='$job_id'";
		mysql_query($sql) or die(mysql_error());
		// We also need to deactivate any associated skills
		$sql = "UPDATE job_skills SET active=0 WHERE job_id='$job_id'";
		mysql_query($sql) or die(mysql_error());
		header("Location:../job_detail.php?job=$job_id");
		exit();
	}
}

// Take care of the job information first so we can ignore it in the skills loop.
$title = mysql_real_escape_string($_POST['title']);
$wage = mysql_real_escape_string($_POST['wage']);
$desc = str_replace("\n","<br>",$_POST['job_description']);
$desc = mysql_real_escape_string($desc);
$date = date('Y-m-d H:i:s');
$expires = date('Y-m-d', strtotime($_POST['lifetime']));
$email = $_SESSION['email'];
$company = $_SESSION['company'];
$name = $_SESSION['name'];
$hours = $_POST['hours'];
$phone = $_SESSION['phone'];

// Check if we are creating or updating
if ($action == 'create') {
	$sql = "INSERT INTO jobs (date,company,contact,job_description,title,wage,hours,expire_date,contact_email,phone,status) VALUES('$date', '$company', '$name', '$desc', '$title', '$wage', '$hours', '$expires', '$email', '$phone', 'active')";
	mysql_query($sql) or die(mysql_error());
	$job_id = mysql_insert_id();
}
// Updating
else if ($action == 'edit') {
	$sql= "UPDATE jobs SET title='$title', wage='$wage', hours='$hours', job_description='$desc', expire_date='$expires' WHERE id='$job_id' AND contact_email='$email'";
	mysql_query($sql) or die(mysql_error());
}

// Now we process the skills

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
// If we made it this far and action==create, we run the mail list
if($action=='create') {
	include_once('mail_list_script.php');
}
header("Location:../job_detail.php?job=$job_id");
?>
