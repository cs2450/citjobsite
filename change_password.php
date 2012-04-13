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
		<table align=center>
			<tr><td colspan=2><br /></td></tr>
			<tr>
				<td style="text-align:right;"><label for="old_password">Old Password: </label></td>
				<td><input type="password" name="old_password" /></td>
			</tr>
			<tr><td colspan=2><br /></td></tr>
			<tr>
				<td style="text-align:right;"><label for="password">New Password: </label></td>
				<td><input type="password" name="password" /></td>
			</tr>
			<tr>
				<td style="text-align:right;"><label for="confirm_password">Confirm Password: </label></td>
				<td><input type="password" name="confirm_password" /></td>
			</tr>
			<tr><td colspan=2><br /></td></tr>
		</table>
		<button class="button" type="submit">Submit</button>
	</form>
</div>
<?php 
}
include_once("inc/footer.php");
?>
