<?php
	include_once('inc/header.php');
	include_once("inc/func.php");

	if (!isset($_SESSION['email']) && !isset($_GET['employer']) ) {
		header("Location:index.php");
		exit();
	}

	$error = isset($_GET['error']) ? $_GET['error'] : NULL;
	$error_type = isset($_GET['type']) ? $_GET['type'] : NULL;
	
	// If students registration or employer registration is complete, let them know their profile is ready for view
	if(isset($_GET['student_register']) && $_GET['student_register'] == true || isset($_GET['employer_register']) && $_GET['employer_register'] == true) {
		echo '<script type="text/javascript">
			$(document).ready(function () { 
				alert("Thank you for registering ' . $_SESSION['name'] . '! You may now view your profile.");
			})
			</script>';
	}
	// Prepare all the differences between the student and employer profile pages here
	// so I dont have to keep asking "if (user_type==student)" OVER AND OVER AGAIN!! (rawr)
	$pic = 'images/empty-100.png';
	if (isset($_GET['employer'])) {
		$email = $_GET['employer'];
		$sql = "SELECT * FROM employers WHERE email='$email'";
		$result=mysql_query($sql) or die(mysql_error());
		$row = mysql_fetch_array($result);
		// Dont show suspended employers
		// For now just kick to index.php
		if($row['access']==-1){
			header("Location:index.php");
			exit();
		}
		$name = $row['company'];
		$desc = $row['description'];
		$desc_header = "About employer's company:";
		$jobs_list = "Employer's posted jobs:";
		if ($row['logo']) {
			$pic = "logos/".$row['logo'];
		};
	}
	else if ($_SESSION['user_type'] == 'student'){
		$email = &$_SESSION['email'];
		$name = $_SESSION['name'];
		$jobs_list = "Matched Jobs<a href='index.php?page=Matches&pagenumber=1'>[more]</a>";
		$desc_header="About me/Resume <a href='resume.php'>[edit]</a>";
		$sql = "SELECT * FROM students WHERE email='$email'";
		$result=mysql_query($sql) or die("cant fetch description");
		$row=mysql_fetch_array($result);
		$desc=$row['description'];
		if ($row['profile_pic']) {
			$pic = 'profile_pics/'.$row['profile_pic'];
		}
	}
	else if ($_SESSION['user_type'] == 'employer'){
		$email = &$_SESSION['email'];
		$name = $_SESSION['company'];
		$jobs_list = "My Posted Jobs <a href='post_job.php?action=create'>[post job]</a>";
		$desc_header="About my company:";
		$sql = "SELECT * FROM employers WHERE email='$email'";
		$result=mysql_query($sql) or die("cant fetch description");
		$row=mysql_fetch_array($result);
		$desc=$row['description'];
		if ($row['logo']) {
			$pic = "logos/".$row['logo'];
		};
	}
?>
<div class="profilePage">
	<span class="error">
	<?php
		if($error == 'extension' && $error_type == 'image')
			echo 'Invalid file type. Please upload a JPG, GIF, or PNG';
	?>
	</span>
	<div class="leftSide">
		<div class="profileImage">
			<img src="<?php echo $pic; ?>" />
<!--<?php
// Don't show this unless I'm logged in as an employer
if ($_SESSION['user_type'] == 'employer' && $_GET['employer'] == $_SESSION['email']) { ?>
			<br /><a href="edit_company.php">[Add a Logo]</a>
<?php
// Don't show this unless I'm logged in as a student
} else if ($_SESSION['user_type'] == 'student' && !isset($_GET['employer'])) { ?>
			<br /><a href="resume.php">[Add a Picture]</a>
<?php
} ?>-->
		</div>
		<div class="profileName">
		<?php echo $name . "&nbsp;";
			if(($_SESSION['user_type'] == 'employer' || $_SESSION['user_type'] == 'student') && !isset($_GET['employer']))
			{
				if($_SESSION['user_type'] == 'employer')
					echo '<a id="edit_profile" href="edit_company.php">[Edit Profile]</a>';
				else
					echo '<a id="edit_profile" href="resume.php">[Edit Profile]</a>';
			}

			else if($_SESSION['user_type'] == 'admin' && !isset($_GET['employer']))
				echo 'You are admin. We are all pretty jealous.';
		?>
		</div>
		<div class="profileContact"><?php echo $email; ?></div>
		<div class="profileAbout leftJustify">
			<div class="centerJustify"><?php
				echo $desc_header; ?></div><?php
			echo $desc; ?></div>
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
	if ($_SESSION['user_type']=='student' && !isset($_GET['employer'])) { 
		// Get the students skills
		$sql = "select * from student_skills where student_id=".$_SESSION['student_id']; 
		$result = mysql_query($sql) or die('cannot find student');;
			
	if ($row=mysql_fetch_array($result)) {
			$sql = "SELECT skill FROM skills WHERE skill_id=".$row['skill_id'];
			while($row=mysql_fetch_array($result)) {
				$sql = $sql." UNION SELECT skill FROM skills WHERE skill_id=".$row['skill_id'];
			}
			$result = mysql_query($sql) or die('cannot fetch skills');

			echo "<div class='studentSkills topDivider'><div>Skills&nbsp;<a href='skills.php'>[edit]</a></div>\n";

			while($row=mysql_fetch_array($result)) {
				echo "<div class='skill threeCols leftJustify'>".$row['skill']."</div>\n";
			}

			// Make sure to grab any skills that they put in manually
			$sql = "SELECT other_skill FROM student_skills WHERE skill_id=0 AND student_id=".$_SESSION['student_id'];
			$result = mysql_query($sql) or die('cannot fetch "other" skills');
			while($row=mysql_fetch_array($result)) {
				if ($row['other_skill'] != ''){
					echo "<div class='skill threeCols leftJustify'>"."*".$row['other_skill']."</div>\n";
				}
			}
			echo "</div>";
		}
	else
		echo '<b>No Skills Specified.</b> <a href="skills.php">[Add skills]</a>';
	}
?>


	</div>
	<div class="rightSide"><!-- title in next command -->
<?php echo "<div class='top centerJustify'>$jobs_list</div>";

	// If employer, display their own posted jobs. 
	if ($_SESSION['user_type']=='employer') {
		$sql="SELECT * FROM jobs WHERE contact_email='$email' ORDER BY date DESC LIMIT 10";
	// else if we are anyone viewing an employers profile
	} else if (isset($_GET['employer'])) {
		$sql="SELECT * FROM jobs WHERE contact_email='$email' AND status='active' ORDER BY date DESC LIMIT 10";
	// else we must be a student, display our matches
	} else if($_SESSION['user_type'] == 'student'){
		// Fetch the first 10 matches
		$sql = fetch_matches($_SESSION['student_id'], 10, 0);
	}
	
	$result = mysql_query($sql) or die(mysql_error());

	while($row=mysql_fetch_array($result)) {
		?>
		<a class="job <?php echo ($row['status'] != 'active') ? "deleted" : ""; ?>" href="job_detail.php?job=<?php echo $row['id']?>">
			<div class="jobTitle"><?php echo $row['title']; ?></div>
			<div class="jobDescription"><?php echo $row['job_description']; ?></div>
		</a>
<?php } ?>
	</div>
</div>

<?php include_once('inc/footer.php'); ?>
