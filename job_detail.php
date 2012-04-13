<?php
	include_once("inc/header.php");
	$job_id = mysql_real_escape_string($_GET['job']);
	$sql="select * from jobs where id='$job_id';";
	$result=mysql_query($sql) or die(mysql_error());
	$row=mysql_fetch_array($result); 		
	$posted=date('M d, Y', strtotime($row["date"]));
	$expire=date('M d, Y', strtotime($row['expire_date']));

	// If we are an employer and own this job then we get some controls
	// These statements will also tell anyone viewing a filled/delete/expired job
	// the respective status at the top.
	if(isset($_GET['unfill']))
	{
		echo "+++++++++++++++++###############3===================";
		// We must either own this job, or be admin to do this
		if($_SESSION['user_type']=='admin' || ($_SESSION['user_type']=='employer' && $row['contact_email']==$_SESSION['email']))
		{
			$sql = "UPDATE jobs SET status='active' WHERE id=".$_GET['unfill_id'];
			mysql_query($sql) or die("Cannot query database: " . mysql_error());
			// Also reactivate any skills associated
			$sql = "UPDATE job_skills SET active=1 WHERE job_id='$job_id'";
			mysql_query($sql) or die(mysql_error());
		}
	}

	if ($_SESSION['user_type'] == 'employer' && $row['contact_email'] == $_SESSION['email'] && $row['status'] != 'filled' && $row['status'] != 'deleted') {
		$control_buttons = "<a href='post_job.php?page=Post%20Job&action=edit&id=$job_id'>edit</a>";
		$control_buttons .= "<a href='#' id='renew'>renew</a>";
		$control_buttons .= "<a href='php/post_job_script.php?action=filled&id=$job_id'>job filled</a>";
		$control_buttons .= "<a href='php/post_job_script.php?action=deleted&id=$job_id'>remove</a>";
		if($row['status'] == 'expired')
			$control_buttons = "This job has expired, and is no longer listed. You may want to renew it.<br/>".$control_buttons;
	}
	else if($row['status'] == 'filled' || $row['status'] == 'deleted')
		$control_buttons = "<span class=\"notice\">This job has been ".$row['status']."</span>";
	else if($row['status'] == 'expired')
		$control_buttons = "<span class=\"notice\">This job has expired</span>";

	// Link employer name to their profile
	$company_link = "<a href='profile.php?employer=".$row['contact_email']."'>".$row['company']."</a>";

	// If we are a student then give them an apply button 
	$apply_button = "";
	if($_SESSION['user_type'] == 'student'){
		$apply_button = "<input type='hidden' name='title' value='".$row['title']."' />\n";
		$apply_button .= "<input type='hidden' name='email' value='".$row['contact_email']."' />\n";
		$apply_button .= "<button class='button' id='apply_button' onclick='return confirm(\"Are you sure?\");'>Apply for this job</button>";
	}

	// Get skill data from skills table
	$sql = "SELECT skill_id, skill FROM skills";
	$result=mysql_query($sql) or die(mysql_error());
	$skills = array();
	while($skill=mysql_fetch_assoc($result))
		$skills[$skill['skill_id']] = $skill['skill'];

	// Grab and format the skills for the job.
	$job_skills = "<div class='studentSkills topDivider'><div><b>Skills</b></div>\n";
	$sql = "SELECT * FROM job_skills WHERE job_id='$job_id'";
	$result=mysql_query($sql) or die(mysql_error());
	while($skill=mysql_fetch_assoc($result)) {
		$job_skills .= "<div class='skill threeCols leftJustify'>". ($skill['skill_id']==0 ? $skill['other_skill'] : $skills[$skill['skill_id']]) ."</div>\n";
	}
	$job_skills .="</div>\n";

	if($_SESSION['user_type']=='admin') { // Show delete button if admin
?>

<div class="admin_controls"><a href="index.php?delete_job=<?php echo $job_id; ?>" onclick="return confirm('Are you sure you want to delete this job?');"><img src="images/red_x.png" /></a></div>
<?php
	} else { // Everyone else gets the report button
		$adminControls = "<div class='admin_controls'><a href='index.php?report_job=$job_id' onclick='return confirm('Are you sure you want to report this job?');'><img src='images/report.png' /></a><br/>Report</div>";
	}
	if($_SESSION['user_type']=='admin' || ($_SESSION['user_type']=='employer' && $row['contact_email']==$_SESSION['email'])) {
?>
<div class="jobControls"><?php echo $control_buttons; echo ($row['status']=='filled') ? ' <input type="hidden" name="unfill_id" value="'.$job_id.'" /><input type="button" name="unfill" value="Unfill?" />':''; ?></div>
<?php 
	} ?>
<div class="jobDetail">
	<input type="hidden" name="job_id" value="<?php echo $job_id; ?>" />
	<div class="leftSide">
		<div class="profileImage">
			<?php
			$email = $row['contact_email'];
			$sql = "SELECT logo FROM employers WHERE email='$email'";
			$res = mysql_query($sql) or die("Cannot query database: " . mysql_error());
			$emp = mysql_fetch_assoc($res);
			$logo = $emp['logo'];				
			?>
			<img src="<?php echo $logo ? 'logos/'.$logo : 'images/empty-100.png'; ?>" />
		</div>
		<div class="full jobInfo">
			<div class="company"><?php echo $company_link; ?></div>
			<div class="jobTitle"><?php echo $row[title]; ?></div>
			<div class="jobDescription"><?php echo $row[job_description]; ?></div>
		</div>
		<div class="jobDetailSkills">
			<?php echo $job_skills; ?>
		</div>
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
		<div class="date" id="expires">Expires: <?php echo $expire; ?></div>
	</div>
	<div class="apply topDivider">
		<?php echo $adminControls; ?>
		<form id='apply' action='php/apply_script.php' method='post'>
			<?php echo $apply_button; ?>
		</form>
	</div>
</div>

<?php include_once("inc/footer.php"); ?>
