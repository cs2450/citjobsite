<?php
include_once("../inc/connect.php");
session_start();

// If a resume file was uploaded, handle the resume placement and validation
if(isset($_FILES['resume']))
{

	// Store file in variable
	$file = $_FILES["resume"];

	// Get file extension
	$ext = substr($file['name'], strripos($file['name'], '.'));
	$ext = strtolower($ext);

	$error = '';

	// If there is a file error, print out an error
	if($file['error'] != UPLOAD_ERR_OK)
	{
		$error = $file['error'];
	}

	else if($ext != '.pdf')
	{
		$error = 'extension';
	}

	else if($file['size'] > 5000000)
	{
		$error = 'size';
	}

	// If an error is set, redirect to resume.php with an error
	if($error != '')	
		header("Location:../resume.php?error=".$error);

	// Else, upload the file
	else
	{
		$document_name = $_SESSION['email'] ."_". Date("Y-m-d_g:i:s") . $ext;
		$id = $_SESSION['student_id'];
		
		// Check the students database to see if there is already a resume. If there is, go to the directory and delete the previous resume to make room for the new one
		$sql = "SELECT resume FROM students WHERE studentID='$id'";
		$result = mysql_query($sql) or die("Cannot query database: " . mysql_error());
		if(mysql_num_rows($result) == 1)
		{
			$row = mysql_fetch_assoc($result);
			if($row['resume'] != NULL)
			{
				if(chdir("../resumes/"))
					$delete_file = @unlink($row['resume']);
			}
		}

		// Move temporary file to resumes 
		if(move_uploaded_file($file['tmp_name'], "../resumes/$document_name"))
		{
			$sql = "UPDATE students SET resume='$document_name' WHERE studentID='$id'";
			mysql_query($sql) or die("Cannot query database: " . mysql_error());
			header("Location:../resume.php?student_register=true");
			exit();
		}
	}
}

// Else handle the resume form information and insert it into the student database.
else
{
	// An unchecked 'notification' checkbox doesn't seem to show up in post
	// So to allow for toggling it we always set to false then true if needed
	$sql = "UPDATE students SET notification=0 WHERE email='".$_SESSION['email']."'";
	mysql_query($sql) or die(mysql_error());
	
	// Dump all inputs to db
	foreach ($_POST as $key => $value) {
		echo $key.": ".$value."<br/>";
		$sql = "UPDATE students SET $key='$value' WHERE email='".$_SESSION['email']."'";
		if ($key == 'notification' && $value="on")
			$sql = "UPDATE students SET $key=1 WHERE email='".$_SESSION['email']."'";
			
		mysql_query($sql) or die(mysql_error());
	}
	if ($_GET['student_register'])
		header("Location:../profile.php?student_register=true");
	else
		header("Location:../profile.php");

	exit();
}
?>
