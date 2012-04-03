<?php
	// A little sad that the only thing this does is allow changes to 
	// the employer company description. There was nowhere else to put it.
	include_once("inc/connect.php");
	include_once("inc/header.php");
	
	if (!isset($_SESSION['email'])) {
		header("Location:prompt_login.php");
		exit();
	}

	// Get the existing description
	$sql = "SELECT description FROM employers WHERE email='".$_SESSION['email']."'";
	$result = mysql_query($sql) or die(mysql_error());
	// Get the number of returned rows. If there weren't any, assume it is because
	// a student is viewing the page.
	$student = false;
	if (mysql_num_rows($result) == 0) {
		$student = true;
	}
	$row = mysql_fetch_array($result);

	if(isset($_GET['error']))
	{
		if($_GET['error'] == 'move_fail')
			echo '<span class="error" style="display: block;">Failure to upload file. Please try again.</span>';
		else if($_GET['error'] == 'extension')
			echo '<span class="error" style="display: block;">Failure to upload file. Improper file type. JPG, GIF, or PNG only.</span>';
	}
?>
<div class="editCompanyForm">
	<form method="post" action="php/edit_company_script.php" enctype="multipart/form-data" name="edit_company">
		<div class="big bold tall text">Select a Logo to Upload (JPG, GIF, PNG only):</div>
		<div>It will only be displayed as 100x100 so don't waste our space by uploading anything bigger.</div>
		<br /><br />
		<input type="file" name="img_upload" /><br /><br />
<?php if (!$student) { ?>
		<hr /><br />
		<p class="big text">Edit Company Description</p><br />
		<textarea rows="10" cols="50" name="description" ><?php echo $row['description']; ?></textarea>
<?php } ?>
		<br />
		<button class="button" id="edit_company_submit">Update</button>
	</form>
</div>
<?php include_once("inc/footer.php"); ?>
