<?php
/*
if(!isset($_SESSION['email']))
	header("Location:prompt_login.php");
*/
include_once("inc/connect.php");
include_once("inc/header.php");
include_once("inc/func.php");
?>
<p class="text">Thank you for registering, <b><?php echo $_SESSION['name']; ?></b>. Now, take some time to fill out your skills so that employers can find you.</p>
<form method="post" name="student_skills" action="php/skills_script.php">
		<table id="skills_table" class="text">
			<tr>
				<td colspan="2">
					<p><b>Fill out what skills you possess to aid employers in finding you.</b></p>
				</td>
			</tr>
			<tr>
				<td colspan="2">Computer Information Technology skills you possess (check all that apply):</td>
			</tr>
			<tr>
				<td colspan="2">
					<br />
					<a href="#" class="sections" id="cs">Computer Science Skills</a>
					<div class="sub" id="cs_list">
						<?php query('cs'); ?>
					</div>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<br />
					<a href="#" class="sections" id="it">Information Technology Skills</a>
					<div class="sub" id="it_list">
						<?php query('it'); ?>
					</div>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<br />
					<a href="#" class="sections" id="vt">Visual Technology Skills</a>
					<div class="sub" id="vt_list">
						<?php query('vt'); ?>
					<div>
				</td>
			</tr>	
			<tr>
				<td colspan="2">
					<br />
					<label for="other">Other Skills: </label>
				</td>
			</tr>
			<tr>
				<td id="other_skills">
					<input type="text" name="other1" /> 
					<input type="button" value="Add Another Skill" id="add" />
				</td>
			</tr>
			<tr>
				<td colspan="2"><br /><button class="button" onclick="document.student_skills.submit()">Submit Skills</button></td>
			</tr>
		</table>
</form>
<?php include_once("inc/footer.php");