<?php
	include_once("inc/header.php");

	// These are the only two lines different from index.php (thus far)
	include_once("inc/func.php");	
	$sql = fetch_matches($_SESSION['student_id'], 20);

	$result = mysql_query($sql) or die(mysql_error());
	
	$expired = date('Y-m-d', strtotime('-120 month'));
?>	
	<div>
<?php
while($row=mysql_fetch_array($result)) {
	if($row['date']  < $expired ){
		mysql_query("UPDATE jobs SET status='expired' where id='$row[id]'");
		mysql_query("UPDATE job_skills SET active=0 where job_id='$row[id]'");
	}
	else {
		// This div encompasses one entire job ?>
		<a class="partial job" href="jobdetail.php?<?php echo $row['id']; ?>">
			<div class="profileImage"></div>
			<div class="rightSide">
				<div class='hours'><div class="bold">Hours</div><?php
					echo $row[hours]; ?></div>
				<div class='wage'><div class="bold">Wage</div><?php
					echo $row[wage]; ?></div>
				<div class="date">Posted: <?php
					echo date(" m/d/y",strtotime($row['date'])); ?>
				</div>
			</div>
			<div class="jobInfo">
				<div class='company'><?php echo $row['company']; ?></div>
				<div class="jobTitle"><?php echo $row['title']; ?></div>
				<div class="jobDescription"><?php
					echo $row['job_description'] ?>
				</div>
			</div>
		</a>
<?php	}
	}
echo "</div>";
include_once("inc/footer.php");
?>
