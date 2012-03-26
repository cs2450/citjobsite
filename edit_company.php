<?php
	// A little sad that the only thing this does is allow changes to 
	// the employer company description. There was nowhere else to put it.
	session_start();
	if (!isset($_SESSION['email'])) {
		header("Location:prompt_login.php");
		exit();
	}
	include_once("inc/connect.php");
	include_once("inc/header.php");

	// Get the existing description
	$sql = "SELECT description FROM employers WHERE email='".$_SESSION['email']."'";
	$result = mysql_query($sql) or die(mysql_error());
	$row = mysql_fetch_array($result);
?>
<form method="post" action="php/edit_company_script.php">
	<textarea rows="10" cols="50" name="description" ><?php echo $row['description']; ?></textarea>
	<button class="button" id="edit_company_submit">Submit</button>
</form>
<?php include_once("inc/footer.php"); ?>
