<?php
include("inc/header.php");
if(isset($_GET['error'])){
	echo $_GET['error'];
}
if(isset($_GET['action'])){
	echo "Your email has been successfully ".$_GET['action'];
}
?>
<div>
	<h2>Use this form if you wish to recieve email notifications of new posted jobs.</h2>
	<br/><br/>
	<form name="mail_list" method="post" action="php/mail_list_opt.php">
		<table align=center>
			<tr>
				<td style="text-align:right;"><label for="name">Name: </label></td>
				<td style="text-align:left;"><input type="text" name="name" /></td>
			</tr>
			<tr>
				<td style="text-align:right;"><label for="email">Email: </label></td>
				<td style="text-align:left;"><input type="text" name="email" /></td>
			</tr>
			<tr>
				<td style="text-align:right;"><label for="confirm_email">Confirm Email: </label></td>
				<td style="text-align:left;"><input type="text" name="confirm_email" /></td>
			</tr>
			<tr><td colspan=2><br /></td></tr>
			<tr>
				<td style="text-align:right;"><label for="opt-in">Add Me: </label></td>
				<td style="text-align:left;"><input type="radio" name="opt" value="in" /></td>
			</tr>
			<tr>
				<td style="text-align:right;"><label for="opt-out">Remove Me: </label></td>
				<td style="text-align:left;"><input type="radio" name="opt" value="out" /></td>
			</tr>
			<tr><td colspan=2><br /></td></tr>
		</table>
		<button class="button" type="submit">Submit</button>
	</form>
</div>
<?php
include("inc/footer.php");
?>
