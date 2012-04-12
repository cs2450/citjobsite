<?php
include_once("inc/header.php");
include_once("inc/func.php");	

// If an admin suspends an employer, handle it here
// Suspending an emplopyer sets their access level to -1
// Suspended employers aren't deleted, they just aren't allowed to login 
// and their profile doesn't show up in the directory

if($_SESSION['user_type'] == 'admin' && isset($_GET['suspend_employer']))
{
	$email = $_GET['suspend_employer'];
	$sql = "UPDATE employers SET access='-1' WHERE email='$email'";	
	mysql_query($sql) or die("Cannot query database: " . mysql_error());

	echo '<script type="text/javascript">
		$(document).ready(function () {
			alert("Employer successfully suspended");
		});
	</script>';	
}

// Grab the page offset, if any, from get
$pagelimit = 10;
$offset = 0;
if(isset($_GET['pagenumber']) && is_numeric($_GET['pagenumber']))
	$offset = ($_GET['pagenumber']-1)*$pagelimit;
if($offset < 0)
	$offset = 0;

$sql="SELECT * FROM employers WHERE access=1 ORDER BY company LIMIT $pagelimit OFFSET $offset";
$result = mysql_query($sql) or die(mysql_error());

// Get the total number of pages available
$maxPages = mysql_query('SELECT COUNT(*) AS r FROM employers WHERE access=1');
$maxPages = mysql_fetch_array($maxPages);
$maxPages = ceil($maxPages['r'] / $pagelimit);

// Get the current page number
$currentPage = isset($_GET['pagenumber']) ? $_GET['pagenumber'] : 1;

// Handle the pagination menu here. it has to be sometime after the sql query
if ($offset > 0) {
	$prev = "<a href='directory.php?pagenumber=".($_GET['pagenumber']-1)."'>[prev page]</a>";
} else {
	$prev = "<span>[prev page]</span>";
}
//if (mysql_num_rows($result) == $pagelimit) {
if ($currentPage < $maxPages) {
	// Takes care of the dead link if there is no get['pagenumber']
	if (!isset($_GET['pagenumber']))
		$pn = 2;
	else
		$pn = $_GET['pagenumber']+1;
	$next = "<a href='directory.php?pagenumber=$pn'>[next page]</a>";
} else {
	$next = "<span>[next page]</span>";
}

echo $prev.' - '.$currentPage.'/'.$maxPages.' - '.$next;

echo "<div>";

while($row=mysql_fetch_array($result)) {
	if($_SESSION['user_type'] == 'admin')
	{	
		echo '<div class="admin_controls"><a href="?suspend_employer='.$row['email'].'" onclick="return confirm(\'Are you sure you want to suspend this employer?\')"><img src="images/red_x.png" /></a></div>';
	}		

	// This div encompasses one entire employer ?>
	<a class="partial job" href="profile.php?employer=<?php echo $row['email']; ?>">
		<div class="profileImage">
			<img src="<?php echo $row['logo'] ? 'logos/'.$row['logo'] : 'images/empty-100.png'; ?>" />			
		</div>
		<div class="rightSide">
			<div class='hours'><div class="bold">Email</div><?php
				echo $row[email]; ?></div>
			<div class='wage'><div class="bold">Phone</div><?php
				echo $row[phone]; ?></div>
		</div>
		<div class="jobInfo">
			<div class='company'><?php echo $row['company']; ?></div>
			<div class="jobTitle"><?php echo $row['title']; ?></div>
			<div class="jobDescription"><?php
				echo $row['description'] ?>
			</div>
		</div>
	</a>
<?php
	}
echo $prev.' - '.$currentPage.'/'.$maxPages.' - '.$next;
echo "</div>";
include_once("inc/footer.php");
?>
