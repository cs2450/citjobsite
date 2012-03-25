<?php
include_once("../inc/connect.php");
session_start();
$doc_root = "../resumes";

foreach($_POST as $key => $value){
	echo $key.": ".$value."<br/>";
}

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
		$document_name = $_SESSION['email'] ."_". Date("Y-m-d g:i:s") . $ext;
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
		if(move_uploaded_file($file['tmp_name'], "$doc_root/$document_name"))
		{
			$sql = "UPDATE students SET resume='$document_name' WHERE studentID='$id'";
			mysql_query($sql) or die("Cannot query database: " . mysql_error());
			header("Location:../profile.php?student_register=true");
			exit();
		}
	}
}

// Else handle the resume form information and insert it into the student database.
else
{
	header("Location:../profile.php?student_register=true");
	exit();
}
?>
