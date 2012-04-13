<?php
include_once("inc/header.php");
// File satisfies specifications 1.0 and 2.0
// Form satisfies 1.1, 1.1.1, 1.1.2, 1.1.2.2, 1.1.3, 1.1.3.1, 1.4
// and 2.1, 2.1.1, 2.1.2, 2.1.2.2, 2.1.3, 2.1.3.1, 2.4
// Set the email variable if someone input their email address into the email field and clicked Register
$email = isset($_GET['email']) ? $_GET['email'] : NULL;
?>
<div id="form_container" class="text">
	<p class="head">Register for the CIT Jobs Board</p>
	<p class="text"><strong>*All fields required </strong>
		<?php if(isset($_GET['error'])) echo $_GET['error']; ?>
	</p>
	<form method="post" name="register" action="php/register_script.php">
		<table>
			<tr><td class="labels"><label for="name">Name:</label></td>
				<td class="inputs"><input type="text" name="name" /></td></tr>
			<tr><td class="labels"><label for="register_email">Email:</label></td>
				<td class="inputs"><input type="text" name="register_email" value="<?php echo $email; ?>" /></td></tr>
			<tr><td class="labels"><label for="confirm_email">Confirm Email:</label></td>
				<td class="inputs"><input type="text" name="confirm_email" /></td></tr>
			<tr><td class="labels"><label for="phone">(10 Digit) Phone Number:</label></td>
				<td class="inputs"><input type="text" name="phone" maxlength="10" size="10" /></td></tr>
			<tr><td class="labels"><label for="register_password">Password:</label></td>
				<td class="inputs"><input type="password" name="register_password" /></td></tr>
			<tr><td class="labels"><label for="confirm_password">Confirm Password:</label></td>
				<td class="inputs"><input type="password" name="confirm_password" /></td></tr>
			<tr><td class="labels"><label for="type">Student/Job Seeker?</label></td>
				<td class="inputs"><input type="radio" name="register_type" id="student_radio" value="student" /></td></tr>
			<tr><td class="labels"><label for="type">Employer?</label></td>
				<td class="inputs"><input type="radio" name="register_type" id="employer_radio" value="employer" /></td></tr>
		</table>
		<br/>
		<?php require_once("inc/recaptcha_client.php"); ?>
		<br/>
		<button class="button" id="register_button">Register</button>
	</form>
</div>
</body>
</html>
