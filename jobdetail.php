<?php
	require_once("inc/connect.php");
	include_once("inc/header.php");
	$i = $_SERVER["QUERY_STRING"];
	$sql="select * from jobs where id='$i';";
	$result=mysql_query($sql);
	$row=mysql_fetch_array($result); 		
	$date=date("M d, Y", strtotime($row["date"]));
?>


<div>
	<div>
		<div>
			<!-- <h2>Title</h2> -->
			<h2><?php echo $row[title]; ?></h2>

		<div>
			<!-- <p>Company</p> -->
			<p><strong><?php echo $row[company]; ?></strong></p>
		</div>
			<!-- <p>Date</p> -->
			<p><strong>Posted: </strong><?php echo $date; ?></p>
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
