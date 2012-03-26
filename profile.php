<?php
	session_start();
	if (!isset($_SESSION['email'])) {
		header("Location:index.php");
		exit();
	}
	include_once('inc/header.php');

	// If students registration or employer registration is complete, let them know their profile is ready for view
	if($_GET['student_register'] == true || $_GET['employer_register'] == true)
	{
		echo '<script type="text/javascript">
			$(document).ready(function () { 
				alert("Thank you for registering ' . $_SESSION['name'] . '! You may now view your profile.");
			})
			</script>';
	}

	// Prepare all the differences between the student and employer profile pages here
	// so I dont have to keep asking "if (user_type==student)" OVER AND OVER AGAIN!! (rawr)

	if ($_SESSION['user_type'] == 'student'){
		$name = $_SESSION['name'];
		$jobs_list = "Jobs List";
		$desc_header="About me/Resume <a href='resume.php'>[edit]</a>";
		$sql = "SELECT description FROM students WHERE email='".$_SESSION['email']."'";
	}
	else if ($_SESSION['user_type'] == 'employer'){
		$name = $_SESSION['company'];
		$jobs_list = "My Posted Jobs List";
		$desc_header="About my company: <a href='edit_company.php'>[edit]</a>";
		$sql = "SELECT description FROM employers WHERE email='".$_SESSION['email']."'";
	}
	else { die('invalid user type'); }
	$result=mysql_query($sql) or die("cant fetch description");
	$desc=mysql_fetch_array($result);
	$desc=$desc['description'];
?>
<div class="profilePage">
	<div class="leftSide">
		<div class="profileImage"></div>
		<div class="profileName">
			<?php echo $name; ?>
		</div>
		<div class="profileContact"><?php echo $_SESSION['email']; ?></div>
		<div class="profileAbout">
			<div class="CHANGEMEPLEASE">
				<?php echo $desc_header; ?>
			</div>
			<?php echo $desc; ?> 
		</div>
<?php
/*
	Sample result from the below PHP based on student skills
		<div class="studentSkills"><div>Skills</div>
			<div class="skill threeCols">C</div>
			<div class="skill threeCols">C++</div>
			<div class="skill threeCols">Java</div>
			<div class="skill threeCols">Php</div>
			<div class="skill threeCols">Javascript</div>
			<div class="skill threeCols">Go</div>
		</div>
*/

// Check to see if user is a student then query and display the skills list
	if ($_SESSION['user_type']=='student') { 
		// Get the students skills
		$sql = "select * from student_skills where student_id=".$_SESSION['student_id']; 
		$result = mysql_query($sql) or die('cannot find student');;

		if ($row=mysql_fetch_array($result)) {
			$sql = "SELECT skill FROM skills WHERE skill_id=".$row['skill_id'];
			while($row=mysql_fetch_array($result)) {
				$sql = $sql." UNION SELECT skill FROM skills WHERE skill_id=".$row['skill_id'];
			}
			$result = mysql_query($sql) or die('cannot fetch skills');

			echo "<div class='studentSkills'><div>Skills <a href='skills.php'>[edit]</a></div>\n";
			while($row=mysql_fetch_array($result)) {
				echo "<div class='skill threeCols'>".$row['skill']."</div>\n";
			}

			// Make sure to grab any skills that they put in manually
			$sql = "SELECT other_skill FROM student_skills WHERE skill_id=0 AND student_id=".$_SESSION['student_id'];
			$result = mysql_query($sql) or die('cannot fetch "other" skills');
			while($row=mysql_fetch_array($result)) {
				if ($row['other_skill'] != ''){
					echo "<div class='skill threeCols'>"."*".$row['other_skill']."</div>\n";
				}
			}
			echo "</div>";
		}
	}
?>


	</div>
	<div class="rightSide"><!-- title in next command -->
<?php echo $jobs_list;
/*
		// Sample output from the below PHP loop

		<div class="job">
			<div class="jobTitle">Example Job</div>
			<div class="jobDescription">
				This is a short fake job.
			</div>
		</div>
		<div class="job">
			<div class="jobTitle">Job Number Two</div>
			<div class="jobDescription">
				This is the next up-and-coming super long fake job.
			</div>
		</div>
		<div class="job">
			<div class="jobTitle">Example Job</div>
			<div class="jobDescription">
				This is a short fake job.
			</div>
		</div>
		<div class="job">
			<div class="jobTitle">Job Number Two</div>
			<div class="jobDescription">
				This is the next up-and-coming super long fake job.
			</div>
		</div>
		<div class="job">
			<div class="jobTitle">Example Job</div>
			<div class="jobDescription">
				This is a short fake job.
			</div>
		</div>
		<div class="job">
			<div class="jobTitle">Job Number Two</div>
			<div class="jobDescription">
				This is the next up-and-coming super long fake job.
			</div>
		</div>
		<div class="job">
			<div class="jobTitle">Example Job</div>
			<div class="jobDescription">
				This is a short fake job.
			</div>
		</div>
		<div class="job">
			<div class="jobTitle">Job Number Two</div>
			<div class="jobDescription">
				This is the next up-and-coming super long fake job.
			</div>
		</div>
*/
	if ($_SESSION['user_type']=='employer') {
		$email = $_SESSION['email'];
		$sql="SELECT * FROM jobs WHERE contact_email='$email' ORDER BY date DESC LIMIT 10";
	} else {
		$sql="SELECT * FROM jobs WHERE status='active' ORDER BY date DESC LIMIT 10";
	}

	$result = mysql_query($sql) or die(mysql_error());

	while($row=mysql_fetch_array($result)) {
		?>
		<div class="job">
			<div class="jobTitle">
				<?php echo '<a href="jobdetail.php?'.$row['id'].'">'.$row['title']; ?></a>
			</div>
			<div class="jobDescription">
				<?php echo $row['job_description']; ?>
			</div>
		</div>
<?php } ?>

	</div>
</div>

<?php>
include_once('inc/footer.php');
?>
