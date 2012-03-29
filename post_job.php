<?php
	session_start();
	if (!isset($_SESSION['email']) && $_SESSION['user_type'] != 'employer') {
		header("Location:index.php");
		exit();
	}
	include_once("inc/connect.php");
	include_once("inc/header.php");
	include_once("inc/func.php");
	$action = &$_GET['action'];

	// $submit_button is what goes in the submit button waaaay down at the bottom
	$submit_button = "Submit Job";
	// Set up our variables so we can auto fill if editing a job
	if ($action == 'edit') {
		$submit_button = "Edit job";
		$job_id = &$_GET['id'];
		$email = &$_SESSION['email'];
		$sql = "SELECT * FROM jobs WHERE id='$job_id' AND contact_email='$email'";
		$result = mysql_query($sql) or die(mysql_error());
		if (!mysql_num_rows($result)){
			// We don't own this job... bye bye!
			header("Location:index.php");
			exit();
		}
		$row = mysql_fetch_array($result);
		$title = $row['title'];
		$wage = $row['wage'];
		$hours = $row['hours'];
		$desc = $row['job_description'];
		
		if($hours=="Full Time")
			$full="Selected";
		else if($hours=="Part Time")
			$part="Selected";
		else if($hours=="Full/Part")
			$both="Selected";
		else if($hours=="Negotiable")
			$nego="Selected";
		else if($hours=="Temporary")
			$temp="Selected";
		else if($hours=="Internship")
			$intern="Selected";

		// Now gather the attached skills and match settings
		$sql = "SELECT * FROM job_skills WHERE job_id='$job_id'";
		$result = mysql_query($sql) or die(mysql_error());
		$current_skills = array();
		$other_skills = array();
		while ($row=mysql_fetch_array($result)) {
			$current_skills[$row['skill_id']] = $row['match_priority'];;
			if ($row['other_skill'] != '') {
				array_push($other_skills,$row['other_skill']);
			}
		}
	}
?>
<p>* required information</p>
<form id="jobEditForm" method="post" action="php/post_job_script.php?<?php echo $_SERVER['QUERY_STRING']; ?>">
	<table align=center>
		<tr><td class="rightJust"><label for="title">*Title/Position:</label></td>
			<td class="leftJust" colspan=2>
				<input type="text" size="50" name="title" value="<?php
					echo $title; ?>"/>
			</td></tr>
		<tr><td class="rightJust"><label for="wage">Wage:</label></td>
			<td class="leftJust"><input type="text" name="wage" value="<?php
				echo $wage; ?>"/></td>
			<td><label for="hours">Hours:</label>
				<select name="hours">
					<option value="">-Select-</option>
					<option value="Full Time" <?php echo $full;?>>Full Time</option>
					<option value="Part Time" <?php echo $part;?>>Part Time</option>
					<option value="Full/Part" <?php echo $both;?>>Full/Part</option>
					<option value="Negotiable"<?php echo$nego;?>>Negotiable</option>
					<option value="Temporary" <?php echo $temp;?>>Temporary</option>
					<option value="Internship"<?php echo$intern;?>>Internship</option>
				</select>
			</td>
		</tr>
	</table>
	<div class="row">
	</div>
	<div class="row">
		<label for="job_description">*Job Description</label>
	</div>
	<textarea rows="10" cols="50" name="job_description"> <?php echo $desc; ?></textarea>
	<br/>
	<label for="lifetime">Expires in:</label>
	<select name="lifetime">
		<option value="+6 month">6 Months</option>
		<option value="+3 month">3 Months</option>
		<option value="+1 month">1 Month</option>
	</select>
	<!-- Skills stuff -->
	<div class="jobSkills">
		<label for="skills_matching"><h4>Skills Matching (optional)</h4></label>
		<a href="">How does it work?</a>
		<table id="skills_table" class="text">
<!--		<tr>
				<td colspan="2">
					<p><b>Fill out what skills you possess to aid employers in finding you.</b></p>
				</td>
			</tr>
-->
			<tr>
				<td colspan="3"><h4>Computer Information Technology skills you require (check all that apply):</h4></td>
			</tr>
			<tr>
				<td class="floatLeft">
					<h3 class="sections" id="cs">Computer Science Skills</h3>
					<div class="sub" id="cs_list">
						<label for="cs_matching">Matching</label><br/>
						<?php query('cs',$current_skills,"job"); ?>
					</div>
				</td>
				<td class="floatLeft">
					<h3 class="sections" id="it">Information Technology Skills</h3>
					<div class="sub" id="it_list">
						<label for="it_matching">Matching</label><br/>
						<?php query('it',$current_skills,"job"); ?>
					</div>
				</td>
				<td class="floatLeft">
					<h3 class="sections" id="vt">Visual Technology Skills</h3>
					<div class="sub" id="vt_list">
						<label for="vt_matching">Matching</label><br/>
						<?php query('vt',$current_skills,"job"); ?>
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
				<td colspan="2"><br /><button class="button" onclick="document.student_skills.submit()"><?php echo $submit_button; ?></button></td>
			</tr>
		</table>
	</div>
</form>
<?php include_once("inc/footer.php"); ?>
