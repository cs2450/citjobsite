<?php
	require_once("inc/connect.php");
	include_once("inc/header.php");
	$i = $_SERVER["QUERY_STRING"];
	$sql="select * from jobs where id='$i';";
	$result=mysql_query($sql);
	$row=mysql_fetch_array($result); 		
	$date=date("M d, Y", strtotime($row["date"]));

	// If we are an employer and own this job then we get some controls
	// These statements will also tell anyone viewing a filled/delete/expired job
	// the respective status at the top.
	if ($_SESSION['user_type'] == 'employer' && $row['contact_email'] == $_SESSION['email'] && $row['status'] != 'filled' && $row['status'] != 'deleted') {
		$control_buttons = "<a href='post_job.php?action=edit&id=$i'>[edit]</a>";
		$control_buttons .= "<a href='php/post_job_script.php?action=renew&id=$i'>[renew]</a>";
		$control_buttons .= "<a href='php/post_job_script.php?action=filled&id=$i'>[job filled]</a>";
		$control_buttons .= "<a href='php/post_job_script.php?action=deleted&id=$i'>[remove]</a>";
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
	<div>
		<div>
			<?php echo $control_buttons; ?>
		</div>
		<div>
			<!-- <h2>Title</h2> -->
			<h2><?php echo $row[title]; ?></h2>

		<div>
			<!-- <p>Company</p> -->
			<p><strong><?php echo $company; ?></strong></p>
		</div>
			<!-- <p>Date</p> -->
			<p><strong>Posted: </strong><?php echo $date; ?></p>
		</div>
		<div>
			Expires: <?php echo $row['expire_date']; ?>
		</div>

		<div>
			<!-- <p>Wage</p> -->
			<p><?php echo $row[wage]; ?></p>
			<!-- <p>Hours</p> -->
			<p><?php echo $row[hours]; ?></p>
		</div>
		<div>
			<!-- Job description -->
			<p><?php echo $row[job_description]; ?></p>
		</div>
		<div>
			<!-- <h4>Contact@contact.com</h4> -->
			<h4><?php echo $row[contact_email]; ?></h4>
		</div>
		<div>
			<!-- <p>800ISeeYou</p> -->
			<p><?php echo $row[phone]; ?></p>
		</div>
	<div>	
</div>


<?php include_once("inc/footer.php"); ?>
