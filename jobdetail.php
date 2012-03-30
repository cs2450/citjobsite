<?php
	include_once("inc/header.php");
	$i = $_SERVER["QUERY_STRING"];
	$sql="select * from jobs where id='$i';";
	$result=mysql_query($sql);
	$row=mysql_fetch_array($result); 		
	$posted=date('M d, Y', strtotime($row["date"]));
	$expire=date('M d, Y', strtotime($row['expire_date']));

	// If we are an employer and own this job then we get some controls
	// These statements will also tell anyone viewing a filled/delete/expired job
	// the respective status at the top.
	if ($_SESSION['user_type'] == 'employer' && $row['contact_email'] == $_SESSION['email'] && $row['status'] != 'filled' && $row['status'] != 'deleted') {
		$control_buttons = "<a href='post_job.php?page=Post%20Job&action=edit&id=$i'>edit</a>";
		$control_buttons .= "<a href='php/post_job_script.php?action=renew&id=$i'>renew</a>";
		$control_buttons .= "<a href='php/post_job_script.php?action=filled&id=$i'>job filled</a>";
		$control_buttons .= "<a href='php/post_job_script.php?action=deleted&id=$i'>remove</a>";
		if($row['status'] == 'expired')
			$control_buttons = "This job has expired, please renew it.<br/>".$control_buttons;
	}
	else if($row['status'] == 'filled' || $row['status'] == 'deleted')
		$control_buttons = "This job has been ".$row['status'];
	else if($row['status'] == 'expired')
		$control_buttons = "This job has expired";

	$company = "<a href='profile.php?employer=".$row['contact_email']."'>".$row['company']."</a>";
?>

<div>
	<div class="jobControls"><?php echo $control_buttons; ?></div>
	<div class="profileImage"></div>
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
</div>

<?php include_once("inc/footer.php"); ?>
