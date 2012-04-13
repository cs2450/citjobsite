<?php
include_once("inc/header.php");
// specifications 3.2, 3.2.1, 3.2.2, 3.2.5, 3.2.6, 3.3
// If the session isn't set by logging in or registering, re-route to login.php

if(!isset($_SESSION['email'])) {
	header("Location:prompt_login.php");
	exit();
}

else if($_SESSION['user_type'] != 'student') {
	header("Location:index.php");
	exit();
}

// Grab the student's resume, if any, so we can display a link to it
// Also grab from the db so we can autofill any inputs
$resume_link = "No resume uploaded";
$email = $_SESSION['email'];
$sql = "SELECT *  FROM students WHERE email='$email'";
$result = mysql_query($sql);
$info = mysql_fetch_array($result);
$resume = $info['resume'];
if ( !($resume == "NULL" || $resume == '') ) {
	$resume_link = "<a href='resumes/$resume'>Current resume</a>";
}
if(isset($_GET['error'])) {
	$printout = '';
	
	// Errors array
	$errors = array(
		UPLOAD_ERR_OK			=> "No errors.", 
		UPLOAD_ERR_INI_SIZE		=> "File is too large.",
		UPLOAD_ERR_FORM_SIZE    => "File is too large.",
		UPLOAD_ERR_PARTIAL		=> "Partial upload.",	
		UPLOAD_ERR_NO_FILE		=> "No file uploaded.",
		UPLOAD_ERR_NO_TMP_DIR	=> "No temporary directory to upload to.",
		UPLOAD_ERR_CANT_WRITE	=> "Can't write to disk.",
		UPLOAD_ERR_EXTENSION	=> "Invalid file type.",
	);
	
	if(is_numeric($_GET['error'])) {
		$printout = $errors[ $_GET['error'] ];
	}
	else {
		if($_GET['error'] == 'extension')
			$printout = 'Invalid file type. PDF only.';
		
		else if($_GET['error'] == 'size')
			$printout = 'File is too large. Max file size is 5MB';
	}
	
	echo '<center><div class="error">A server error occured: '. $printout . '</div></center><br />';
}
$pic = 'images/empty-100.png';
if ($info['profile_pic']) {
	$pic = 'profile_pics/'.$info['profile_pic'];
}
?>
<form method="post" name="upload" action="php/resume_script.php" enctype="multipart/form-data">
<table id="resume_table">
	<tr>
		<td colspan=2><br /><h1>Now, take some time to upload a resume and fill out some basic information.</h1><br /><h3>While nothing on this page is required, more information can improve your chances for employment.</h3><br />-OR-<br /><a href="profile.php">Continue to Profile.</a></td>
	</tr>
	<tr>
		<td colspan=2><hr /></td>
	</tr>
	<tr>
		<td style="width:50%;"><br />
			<label for="phone">Contact Phone Number: </label>
			<input type="text" name="phone" maxlength="10" size="10" value="<?php echo $info['phone']; ?>"/> (10 digits)</td>
		<td style="width:50%;"><br />
			<label for="contact_email">Contact Email: </label>
			<input type="text" name="contact_email" size="40" value="<?php echo $info['contact_email']; ?>" /></td>
	</tr>
	<tr>
		<td colspan=2 style="text-align: left; padding-left: 27%;"><br />
			<input type="checkbox" name="hide_info" id="hide_contact" <?php echo ($info['hide_info']) ? "checked": ""; ?> /> Hide my contact information from potential employers?</td>
	</tr>
	<tr>
		<td colspan=2 style="text-align: left; padding-left: 27%;">
			<input type="checkbox" name="notification" <?php if($info['notification']){ echo "checked"; } ?>/> Receive new job notifications via email?</td>
	</tr>
	<tr>
		<td colspan=2><hr /></td>
	</tr>
	<tr>
		<td><b>Upload your profile picture: (.jpg, .gif, .png only)</b>
		<td><b>Upload your resume: (.pdf only)</b></td>
	</tr>
	<tr>
		<td>It will only be displayed as 100x100 pixels.</td>
		<td></td>
	</tr>
	<tr>
		<td><br /><img src="<?php echo $pic; ?>" /></td>
		<td><br /><?php echo $resume_link; ?></td>
	</tr>
	<tr>
		<td><br /><input type="file" name="image" /></td>
		<td><br /><input type="file" name="resume" /><br />
		</td>
	</tr>
	<tr>
		<td style="text-align: right;"></td>
		<td style="text-align: left;"></td>
	</tr>
	<tr>
		<td colspan=2><br /><hr /></td>
	</tr>
	<tr>
		<td><b>Brief description of yourself:</b></td>
		<td><b>Brief list of employable qualities:</b></td>
	</tr>
	<tr>
		<td><br /><textarea rows="10" cols="50" name="description" ><?php echo $info['description']; ?></textarea></td>
		<td><br /><textarea rows="10" cols="50" name="qualities"><?php echo $info['qualities']; ?></textarea></td>
	</tr>
	<tr>
		<td colspan=2><br /><br /><button class="button" id="resume_submit">Submit Info</button></td>
	</tr>
</table>
</form>
<?php include_once("inc/footer.php"); ?>
