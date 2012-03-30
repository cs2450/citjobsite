<?php
/*
if(!isset($_SESSION['email']))
	header("Location:prompt_login.php");
*/
include_once("inc/header.php");
include_once("inc/func.php");

// If the session isn't set by logging in or registering, re-route to login.php
if(!isset($_SESSION['email']) || $_SESSION['user_type'] != 'student') {
	header("Location:index.php?page=home");
	exit();
}

// This variable is used to pass on the get variables
$get = '';
if ($_GET['student_register'] == true) {
	echo "<div class='centerJustify big tall text'>Thank you for registering ".$_SESSION['name'].".</div><div class='centerJustify big tall text'>Now, take some time to fill out your skills so that employers can find you.</div>";
	$get = "?".$_SERVER['QUERY_STRING'];
}
// Get the students current skills so we can auto check them when editing
if ($_SESSION['user_type'] == 'student') {
	$id = $_SESSION['student_id'];
	$sql = "SELECT skill_id, other_skill FROM student_skills WHERE student_id='".$id."'";
	$result= mysql_query($sql) or die("failed fetching skills");
	$current_skills = array();
	$other_skills = array();
	while ($row=mysql_fetch_array($result)) {
		$current_skills[$row['skill_id']] = true;
		if ($row['other_skill'] != '') {
			array_push($other_skills,$row['other_skill']);
		}
	}
}
?>
<form method="post" name="student_skills" action="php/skills_script.php<?php echo $get; ?>">
		<table id="skills_table" class="text" align=center>
			<tr>
				<td colspan="2" class="top">Computer Information Technology skills you possess (check all that apply):</td>
			</tr>
			<tr>
				<td class="floatLeft">
					<h3 class="sections" id="cs">Computer Science Skills</h3>
					<div class="sub" id="cs_list">
						<?php query('cs',$current_skills); ?>
					</div>
				</td>
				<td class="floatLeft">
					<h3 class="sections" id="it">Information Technology Skills</h3>
					<div class="sub" id="it_list">
						<?php query('it',$current_skills); ?>
					</div>
				</td>
				<td class="floatLeft">
					<h3 class="sections" id="vt">Visual Technology Skills</h3>
					<div class="sub" id="vt_list">
						<?php query('vt',$current_skills); ?>
					<div>
				</td>
			<tr>
				<td colspan="2">
					<br />
					<label for="other">Other Skills: </label>
				</td>
			</tr>
			<tr>
				<td id="other_skills">
					<input type="button" value="Add Another Skill" id="add" />
						<?php  if (count($other_skills) != 0){
							// Hidden value is the number of 'other_skills
							// pulled from db. Value used in jobs.js
							echo '<input type="hidden" name="other_count" id="other_count" value="'.count($other_skills).'" />';
							for ($c = 0;$c < count($other_skills);$c++) {
								echo '<br /><input type="text" id="other'.($c+1).'" name="other'.($c+1).'" value="'.$other_skills[$c].'"><input type="button" value="Remove" id="skill_remove'.($c+1).'" class="skill_remove" />';
						}} ?>
				</td>
			</tr>
			<tr>
				<td colspan="2"><br /><button class="button" onclick="document.student_skills.submit()">Submit Skills</button></td>
			</tr>
		</table>
</form>
<?php include_once("inc/footer.php");
