<?php
include_once("inc/header.php");
?>
	<p class="head">Register for the CIT Jobs Board</p>
	<p class="text"><strong>*All fields required</strong></p>
<div id="form_container" class="text">
	<?php
		if(isset($_GET['error']))
			echo '<span class="error">'.$_GET['msg'].'</span>';
	?>
	<form method="post" name="register" action="php/register_script.php">
		<div class="labels">
			<label for="name">Name:</label>
			<label for="register_email">Email:</label>
			<label for="confirm_email">Confirm Email:</label>
			<label for="phone">(10 Digit) Phone Number:</label>
			<label for="register_password">Password:</label>
			<label for="confirm_password">Confirm Password:</label>
			<label for="type">Student/Job Seeker?</label>
			<label for="type">Employer?</label>
		</div>

		<div class="inputs">
			<input type="text" name="name" />
			<input type="text" name="register_email" />
			<input type="text" name="confirm_email" />
			<input type="text" name="phone" maxlength="10" size="10" />
			<input type="password" name="register_password" />
			<input type="password" name="confirm_password" />
		</div>
		
		<div class="radios">
			<input type="radio" name="register_type" id="student_radio" value="student" />
			<input type="radio" name="register_type" id="employer_radio" value="employer" />
			<button class="button" id="register_button">Register</button>
		</div>
	</form>
</div>
</body>
</html>
