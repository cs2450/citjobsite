<?php
include_once("inc/header.php");
include_once("inc/func.php");	

// Grab the page offset, if any, from get
$pagelimit = 10;
$offset = 0;
if(isset($_GET['pagenumber']) && is_numeric($_GET['pagenumber']))
	$offset = ($_GET['pagenumber']-1)*$pagelimit;
if($offset < 0)
	$offset = 0;

if($_SESSION['user_type'] == 'student' && $_GET['page'] == "Matches")
	$sql = fetch_matches($_SESSION['student_id'], $pagelimit, $offset);
else
	$sql="SELECT * FROM jobs WHERE status='active' ORDER BY date DESC LIMIT $pagelimit OFFSET $offset";

$result = mysql_query($sql) or die(mysql_error());

// Handle the pagination menu here. it has to be sometime after the sql query
if($offset > 0)
	$prev = "<a href='index.php?page=".$_GET['page']."&pagenumber=".($_GET['pagenumber']-1)."'>[prev]</a>";
else
	$prev = "[prev]";
if(mysql_num_rows($result) == $pagelimit){
	// Takes care of the dead link if there is no get['pagenumber']
	if (!isset($_GET['pagenumber']))
		$pn = 2;
	else
		$pn = $_GET['pagenumber']+1;
	$next = "<a href='index.php?page=".$_GET['page']."&pagenumber=$pn'>[next]</a>";
}else
	$next = "[next]";

echo $prev." ".$next;


$expired = date('Y-m-d', strtotime('-120 month'));

echo "<div>";

while($row=mysql_fetch_array($result)) {
	if($row['date']  < $expired ){
		mysql_query("UPDATE jobs SET status='expired' where id='$row[id]'");
		mysql_query("UPDATE job_skills SET active=0 where job_id='$row[id]'");
	}
	else {
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
echo $prev." ".$next;
echo "</div>";
include_once("inc/footer.php");
?>
