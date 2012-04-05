<?php
	include_once("inc/header.php");
	$i = mysql_real_escape_string($_SERVER["QUERY_STRING"]);
	$sql="select * from jobs where id='$i';";
	$result=mysql_query($sql) or die(mysql_error());
	$row=mysql_fetch_array($result); 		
	$posted=date('M d, Y', strtotime($row["date"]));
	$expire=date('M d, Y', strtotime($row['expire_date']));
	// TODO
	// There is no way to link the jobs table to the employers table in order to
	// extract the company logo.
	// - The emails are not guaranteed to be the same.
	// - The contact is not guaranteed to be the same name as the employer.
	// - The company name is not guaranteed to be unique.

	// If we are an employer and own this job then we get some controls
	// These statements will also tell anyone viewing a filled/delete/expired job
	// the respective status at the top.
	if ($_SESSION['user_type'] == 'employer' && $row['contact_email'] == $_SESSION['email'] && $row['status'] != 'filled' && $row['status'] != 'deleted') {
		$control_buttons = "<a href='post_job.php?page=Post%20Job&action=edit&id=$i'>edit</a>";
		$control_buttons .= "<a href='php/post_job_script.php?action=renew&id=$i'>renew</a>";
		$control_buttons .= "<a href='php/post_job_script.php?action=filled&id=$i'>job filled</a>";
		$control_buttons .= "<a href='php/post_job_script.php?action=deleted&id=$i'>remove</a>";
		if($row['status'] == 'expired')
			$control_buttons = "This job has expired, and is no longer listed. You may want to renew it.<br/>".$control_buttons;
	}
	else if($row['status'] == 'filled' || $row['status'] == 'deleted')
		$control_buttons = "This job has been ".$row['status'];
	else if($row['status'] == 'expired')
		$control_buttons = "This job has expired";

	// Link employer name to their profile
	$company = "<a href='profile.php?employer=".$row['contact_email']."'>".$row['company']."</a>";

	// If we are a student then give them an apply button 
	$apply_button = "";
	if($_SESSION['user_type'] == 'student'){
		$apply_button = "<input type='hidden' name='title' value='".$row['title']."' />\n";
		$apply_button .= "<input type='hidden' name='email' value='".$row['contact_email']."' />\n";
		$apply_button .= "<button class='button' id='apply_button'>Apply for this job</button>";
	}

	// Get skill data from skills table
	$sql = "SELECT skill_id, skill FROM skills";
	$result=mysql_query($sql) or die(mysql_error());
	$skills = array();
	while($skill=mysql_fetch_assoc($result))
		$skills[$skill['skill_id']] = $skill['skill'];

	// Grab and format the skills for the job.
	$job_skills = "<div class='studentSkills topDivider'><div>Skills</div>\n";
	$sql = "SELECT * FROM job_skills WHERE job_id='$i'";
	$result=mysql_query($sql) or die(mysql_error());
	while($skill=mysql_fetch_assoc($result)) {
		$job_skills .= "<div class='skill threeCols leftJustify'>".$skills[$skill['skill_id']]."</div>\n";
	}
	$job_skills .="</div>\n";
?>
<div>
	<div class="jobControls"><?php echo $control_buttons; ?></div>
	<div class="profileImage">
		<img src="<?php echo $logo ? "logos/$logo" : "images/empty-100.png"; ?>" />
	</div>
	<div class="full rightSide">
		<div class="hours"><div class="bold">Hours</div><?php
			echo $row['hours']; ?></div>
		<div class="wage tall"><div class="bold">Wage</div><?php
			echo $row['wage']; ?></div>
		<div class="email"><div class="bold">Contact Email</div><?php
			echo $row[contact_email]; ?></div>
		<div class="phone tall"><div class="bold">Contact Phone</div><?php
			echo $row[phone]; ?></div>
		<div class="date">Posted: <?php echo $posted; ?></div>
		<div class="date">Expires: <?php echo $expire; ?></div>
	</div>
	<div class="full jobInfo">
		<div class="company"><?php echo $company; ?></div>
		<div class="jobTitle"><?php echo $row[title]; ?></div>
		<div class="jobDescription"><?php echo $row[job_description]; ?></div>
	</div>
	<div class="jobDetailSkills">
		<?php echo $job_skills; ?>
	</div>
	<div class="apply">
		<form id='apply' action='php/apply_script.php' method='post'>
			<?php echo $apply_button; ?>
		</form>
	</div>
</div>

<?php include_once("inc/footer.php"); ?>
