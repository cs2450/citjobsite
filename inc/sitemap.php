<div class="sitemap">
<?php
if(isset($_SESSION['email'])) {
	echo '<div><a href="change_password.php">Change Password</a></div>';
} else {
	echo '<div><a href="forgotten_password.php">Forgot Password</a></div>';
}
?>
	<div><a href="directory.php">Employer Directory</a></div>
	<div><a href="privacy.php">Privacy Policy</a></div>
	<div><a href="faq.php">FAQ</a></div>
</div>
