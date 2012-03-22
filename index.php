<?php
	require_once("inc/connect.php");
	include_once("inc/header.php");
	
	$sql="SELECT * FROM jobs WHERE status='active' ORDER BY date DESC LIMIT 20";
	$result = mysql_query($sql) or die(mysql_error());
	
	$expired = date('Y-m-d', strtotime('-120 month'));
?>	
	<!-- This div is for the labels -->
	<div class="jobHeader">
		<div class="position">Position</div>
		<div class="hours">Hours</div>
		<div class="wage">Wage</div>
	</div>

	<!-- This div encompasses the entire list -->
	<div>
<?php
	while($row=mysql_fetch_array($result)) {
		if($row['date']  < $expired ){
			mysql_query("UPDATE jobs SET status='expired' where id='$row[id]'");
		}
		else {
			// This div encompasses one entire job
			echo '<div class="job">';
				echo "<div class='jobTitle'><a href='jobdetail.php?$row[id]'>$row[title]</a></div>";
				echo "<div>".date("m/d/y",strtotime($row['date']))."</div>";
				echo "<div>$row[company]</div>";
				echo "<div class='hours'>$row[hours]</div>";
				echo "<div class='wage'>$row[wage]</div>";
			echo "</div>";
		}
	}
	echo "</div>";
	include_once("inc/footer.php");
?>
