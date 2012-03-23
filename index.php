<?php
	include_once("inc/header.php");
	
	$sql="SELECT * FROM jobs WHERE status='active' ORDER BY date DESC LIMIT 20";
	$result = mysql_query($sql) or die(mysql_error());
	
	$expired = date('Y-m-d', strtotime('-120 month'));
?>	
	<!-- This div is for the labels -->
<!--
	<div class="jobHeader">
		<div class="position">Position</div>
		<div class="hours">Hours</div>
		<div class="wage">Wage</div>
	</div>
-->
	<!-- This div encompasses the entire list -->
	<div>
<?php
while($row=mysql_fetch_array($result)) {
	if($row['date']  < $expired ){
		mysql_query("UPDATE jobs SET status='expired' where id='$row[id]'");
	}
	else {
		// This div encompasses one entire job ?>
		<div class="job">
			<div class="profileImage"></div>
			<div class="leftSide">
				<div class="jobTitle"><?php
					echo "<a href='jobdetail.php?$row[id]'>$row[title]</a>"; ?>
				</div>
				<div class="jobDescription"><?php
					echo $row['job_description'] ?>
				</div>
				<div class='company'><?php echo $row[company]; ?></div>
			</div>
			<div class="rightSide">
				<div class='hours'><div>Hours</div><?php echo $row[hours]; ?></div>
				<div class='wage'><div>Wage</div><?php echo $row[wage]; ?></div>
				<div class="date">Expires: &nbsp;<?php
					echo date(" m/d/y",strtotime($row['date'])); ?>
				</div>
			</div>
		</div>
<?php	}
	}
echo "</div>";
include_once("inc/footer.php");
?>
