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
		<label for="name">Name: </label>
		<input type="text" name="name" />
		<br/><br/>
		<label for="email">Email: </label>
		<input type="text" name="email" />
		<br/><br/>
		<label for="confirm_email">Confirm Email: </label>
		<input type="text" name="confirm_email" />
		<br/><br/>
		<label for="opt-in">Add Me: </label>
		<input type="radio" name="opt" value="in" />
		<br/><br/>
		<label for="opt-out">Remove Me: </label>
		<input type="radio" name="opt" value="out" />
		<br/><br/><br/>
		<button class="button" type="submit">Submit</button>
	</form>
</div>
<?php
include("inc/footer.php");
?>
