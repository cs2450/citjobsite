<?php
include_once("inc/header.php");
if(!isset($_SESSION['email'])) {
	    header("Location:../prompt_login.php");
		    exit();
}   
// Allow users to change their password
if(isset($_GET['success']) && $_GET['success']=true) {
	echo "Password changed successfully<br/>";
	echo "Return <a href='profile.php?page=home'>home</a>";
}
else  
{
	if(isset($_GET['error'])) {
		echo $_GET['error'];
	}
?>
<div>
	<form name="change_pass" method="post" action="php/change_pass_script.php">
		<label for="old_password">Old Password: </label>
		<input type="password" name="old_password" />
		<br/>
		<label for="password">New Password: </label>
		<input type="password" name="password" />
		<br/>
		<label for="confirm_password">Confirm Password: </label>
		<input type="password" name="confirm_password" />
		<br/><br/>
		<button class="button" type="submit">Submit</button>
	</form>
</div>
<?php 
}
include_once("inc/footer.php");
?>
