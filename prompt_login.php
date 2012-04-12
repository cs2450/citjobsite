<?php
include_once("inc/header.php");
$msg = 'Please login to access this page.';
if ($_GET['error'] == 'bad_info') {
	$msg = 'Invalid email or password.<br/> ';
	$msg .= "<a href='forgotten_password.php'>Forgotten your password?</a>";
}
else if($_GET['error'] == 'account_suspended'){
	$msg = "Account suspended by Admin";
}
?>
<div>
	<img src="images/prompt-login-arrow.gif" />
	<div class="promptLoginMsg"><?php echo $msg ?></div>
</div>
<?php
include_once("inc/footer.php");
?>
