<?php
	require_once("../inc/connect.php");
	session_start();
	
	$file = $_FILES['img_upload'];
	$document_name = null;
	
	if(isset($file) && $file['error'] != 4)
	{
		// Get file extension
		$ext = substr($file['name'], strripos($file['name'], '.'));
		$ext = strtolower($ext);
		
		$error = '';

		if($ext != '.jpg' && $ext != '.jpeg' && $ext != '.gif' && $ext != '.png')
		{
			$error = 'extension';
		}

		// If an error is set, redirect to edit_company.php with an error
		if($error != '')
			header("Location:../edit_company.php?error=".$error);

		// Else, upload the file
		else
		{
			$document_name = $_SESSION['email'] ."_". Date("Y-m-d_g:i:s") . $ext;
			$company = $_SESSION['company'];
		
			// Check the database to see if there is already a logo. If there is,
			// go to the directory and delete the previous resume to make room for
			// the new one
			$sql = "SELECT logo FROM employers WHERE company='$company'";
			$result = mysql_query($sql) or die("Cannot query database: " . mysql_error());
			if(mysql_num_rows($result) == 1)
			{
				$row = mysql_fetch_assoc($result);
				if($row['logo'] != NULL)
				{
					if(chdir("../logos/"))
						$delete_file = @unlink($row['logo']);
				}
			}

			// Move temporary file to resumes 
			if(!move_uploaded_file($file['tmp_name'], "../logos/$document_name"))
			{
				header("Location:../edit_company.php?error=move_fail");
			}
		}
	}
	
	$desc = mysql_real_escape_string($_POST['description']);
	$sql = "UPDATE employers SET description='$desc', logo='$document_name' WHERE email='".$_SESSION['email']."'";
	mysql_query($sql) or die(mysql_error());
	//header("Location:../profile.php");
?>
