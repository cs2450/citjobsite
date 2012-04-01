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
	$row = mysql_fetch_array($result);

	if(isset($_GET['error']))
	{
		if($_GET['error'] == 'move_fail')
			echo '<span class="error" style="display: block;">Failure to upload file. Please try again.</span>';
		else if($_GET['error'] == 'extension')
			echo '<span class="error" style="display: block;">Failure to upload file. Improper file type. JPG, GIF, or PNG only.</span>';
	}
?>
<form method="post" action="php/edit_company_script.php" enctype="multipart/form-data" style="float:left;" name="edit_company">
	<p class="big text">Select a Logo to Upload (JPG, GIF, PNG only):</p><br />
	<input type="file" name="img_upload" /><br /><br />
	<hr /><br />
	<p class="big text">Edit Company Description</p><br />
	<textarea rows="10" cols="50" name="description" ><?php echo $row['description']; ?></textarea><br />
	<button class="button" id="edit_company_submit">Update</button>
</form>
<?php include_once("inc/footer.php"); ?>
