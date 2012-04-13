<?php
include_once("inc/header.php");
// Allow users to reset their password if forgotten
if(isset($_GET['check_email'])) {
	echo "Your key has been emailed to you";
}
elseif(isset($_GET['success'])) {
	echo "Account recovery successful! Please check your email.";
}
elseif(isset($_GET['error'])) {
	echo $_GET['error'];
}
?>
<div>
	<h1>If you have forgotten your password, you may use this form to recover your account.</h1>
	<br/>An email will be sent to you with a new password. It is strongly recomended that you log in and reset it as soon as possible.<br/><br/><br/>
	<form name="reset_pass" method="post" action="php/reset_pass_script.php">
		<label for="email">Account Email Address: </label>
		<input type="text" name="email" />
		<br/>
		<label for="confirm_email">Confirm Email Address: </label>
		<input type="text" name="confirm_email" />
		<br/><br/>
		<?php require_once("inc/recaptcha_client.php"); ?>
		<br/><br/>
		<button class="button" type="submit">Submit</button>
	</form>
</div>
<?php
include_once("inc/footer.php");
?>
