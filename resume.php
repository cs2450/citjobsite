<?php
include_once("inc/connect.php");
include_once("inc/header.php");

// check the session variables to see what forms to display
session_start();

// If the session isn't set by logging in or registering, re-route to login.php
if(!isset($_SESSION['email'])) {
	header("Location:prompt_login.php");
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
if ( !($resume == "NULL" || $resume == '') )
	$resume_link = "<a href='$resume'>Current resume</a>";

if(isset($_GET['error'])) 
	{
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
		
		if(is_numeric($_GET['error']))
			$printout = $errors[ $_GET['error'] ];
		
		else
		{
			if($_GET['error'] == 'extension')
				$printout = 'Invalid file type. PDF only.';
			
			else if($_GET['error'] == 'size')
				$printout = 'File is too large. Max file size is 5MB';
		}
		
		echo '<center><div class="error">A server error occured: '. $printout . '</div></center><br />';
	}
?>
<form method="post" name="upload" action="php/resume_script.php" enctype="multipart/form-data">
<table id="resume_table" class="text">
	<tr>
		<td>Now, take some time to upload a resume and fill out some basic information in the fields below. While nothing on this page is required, more information can improve your chances for employment. Or <a href="profile.php">continue to profile</a></td>
	</tr>
	<tr>
		<td><p><b>Upload your resume: (.pdf only)</b></p></td>
	</tr>
	<tr>
		<td><?php echo $resume_link; ?></td>
	</tr>
	<tr>
		<td><br /><input type="file" name="resume" /></td>
	</tr>
	<tr>
		<td><br /><input type="button" class="button" id="upload_button" value="Upload Resume" /></td>
	</tr>
</table>
</form>
<form method="post" action="php/resume_script.php">
<table class="text">
	<tr>
		<td><br /><p><b>Fill out your information below:</b></p></td>
	</tr>
	<tr>
		<td><hr /></td>
	</tr>
	<tr>
		<td>Brief description of yourself:</td>
	</tr>
	<tr>
		<td><textarea rows="5" cols="50" name="description" ><?php echo $info['description']; ?></textarea></td>
	</tr>
	<tr>
		<td><br />Brief list of employable qualities:</td>
	</tr>
	<tr>
		<td><textarea rows="10" cols="50" name="qualities"><?php echo $info['qualities']; ?></textarea></td>
	</tr>
	<tr>
		<td><br />Employment history:</td>
	</tr>
	<tr>
		<td>Hide contact information from potential employers? <input type="checkbox" id="hide_contact" /></td>
	</tr>
	<tr>
		<td>
		<br />
		<div id="contact_info">
			<label for="phone">Contact Phone Number: </label><input type="text" name="phone" maxlength="10" size="10" value="<?php echo $info['contact_phone']; ?>"/> (10 digits)<br />
			<label for="contact_email">Contact Email: </label><input type="text" name="contact_email" size="40" value="<?php echo $info['contact_email']; ?>" />
		</div>
		</td>
	</tr>
	<tr>
		<td><br />Receive new job notifications via email? <input type="checkbox" name="notification" <?php if($info['notification']){ echo "checked"; } ?>/></td>	
	</tr>
	<tr>
		<td><br /><button class="button" id="resume_submit">Submit Resume</button></td>
	</tr>
</table>
</form>
<?php include_once("inc/footer.php"); ?>
