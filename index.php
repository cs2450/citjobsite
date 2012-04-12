<?php
// Default the page to home
if(!isset($_GET['page']))
	$_GET['page'] = 'home';

include_once("inc/header.php");
include_once("inc/func.php");	
// If an admin deletes a job posting, handle it here

if($_SESSION['user_type'] == 'admin' && isset($_GET['delete_job']))
{
	$job_id = $_GET['delete_job'];
	$sql = "UPDATE jobs SET status='deleted' WHERE id='$job_id'";	
	mysql_query($sql) or die("Cannot query database: " . mysql_error());
	// We also need to deactivate any associated skills
	// If these are not kept in sync, it will show up in matches
	$sql = "UPDATE job_skills SET active=0 WHERE job_id='$job_id'";
	mysql_query($sql) or die(mysql_error());

	echo '<script type="text/javascript">
		$(document).ready(function () {
			alert("Job successfully deleted");
		});
	</script>';	
}

// If an someone reports a job posting, handle it here.

if(isset($_GET['report_job']))
{
	if(isset($_SESSION['email'])) {
		$job_id = $_GET['report_job'];

		// Do something here. Email?

		echo '<script type="text/javascript">
			$(document).ready(function () {
			alert("Job reported. An admin has been alerted. Thank you!");
			});
			</script>';
	}
	else { // We are not logged in
		echo '<script type="text/javascript">
			$(document).ready(function () {
			alert("You must be logged in to report a job.");
			});
			</script>';
	}

}

// Grab the page offset, if any, from get
$pagelimit = 10;
$offset = 0;
if(isset($_GET['pagenumber']) && is_numeric($_GET['pagenumber']))
	$offset = ($_GET['pagenumber']-1)*$pagelimit;
if($offset < 0)
	$offset = 0;

if($_SESSION['user_type'] == 'student' && $_GET['page'] == "Matches") {
	$sql = fetch_matches($_SESSION['student_id'], $pagelimit, $offset, $maxPages);
} else {
	$sql="SELECT * FROM jobs WHERE status='active' ORDER BY date DESC LIMIT $pagelimit OFFSET $offset";
	// Get the total number of pages available
	$maxPages = mysql_query('SELECT COUNT(*) AS r FROM jobs WHERE status="active"');
	$maxPages = mysql_fetch_array($maxPages);
	$maxPages = ceil($maxPages['r'] / $pagelimit);
}

$result = mysql_query($sql) or die(mysql_error());



// Get the current page number
$currentPage = isset($_GET['pagenumber']) ? $_GET['pagenumber'] : 1;

// Handle the pagination menu here. it has to be sometime after the sql query
if ($offset > 0) {
	$prev = "<a href='index.php?page=".$_GET['page']."&pagenumber=".($_GET['pagenumber']-1)."'>[prev page]</a>";
	/*$prev = "<a class='prev arrow' href='index.php?page=".$_GET['page']."&pagenumber=".($_GET['pagenumber']-1)."'></a>";*/
} else {
	$prev = "<span>[prev page]</span>";
	/*$prev = "<img class='prev arrow' src='images/blank.png' />";*/
}
//if (mysql_num_rows($result) == $pagelimit) {
if ($currentPage < $maxPages) {
	// Takes care of the dead link if there is no get['pagenumber']
	if (!isset($_GET['pagenumber']))
		$pn = 2;
	else
		$pn = $_GET['pagenumber']+1;
	$next = "<a href='index.php?page=".$_GET['page']."&pagenumber=$pn'>[next page]</a>";
	/*$next = "<a class='next arrow' href='index.php?page=".$_GET['page']."&pagenumber=$pn'></a>";*/
} else {
	$next = "<span>[next page]</span>";
	/*$next = "<img class='next arrow' src='images/blank.png'/>";*/
}

echo $prev.' - '.$currentPage.'/'.$maxPages.' - '.$next;

$expired = date('Y-m-d', strtotime('-120 month'));

echo "<div>";

while($row=mysql_fetch_array($result)) {
	if($row['date']  < $expired ){
		mysql_query("UPDATE jobs SET status='expired' where id='$row[id]'");
		mysql_query("UPDATE job_skills SET active=0 where job_id='$row[id]'");
	}
	else {				
		if($_SESSION['user_type'] == 'admin')
		{	
			echo '<div class="admin_controls"><a href="?page=Jobs&delete_job='.$row['id'].'" onclick="return confirm(\'Are you sure you want to delete this 			job?\')"><img src="images/red_x.png" /></a></div>';
		}		

		// This div encompasses one entire job ?>
		<a class="partial job" href="job_detail.php?job=<?php echo $row['id']; ?>">
			<div class="profileImage">
				<?php
					$company = addslashes($row['company']);
					$sql = "SELECT logo FROM employers WHERE company='$company'";
					$res = mysql_query($sql) or die("Cannot query database: " . mysql_error());
					$emp = mysql_fetch_assoc($res);
					$logo = $emp['logo'];				
				?>
				<img src="<?php echo $logo ? 'logos/'.$logo : 'images/empty-100.png'; ?>" />			
			</div>
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
echo $prev.' - '.$currentPage.'/'.$maxPages.' - '.$next;
echo "</div>";
include_once("inc/footer.php");
?>
