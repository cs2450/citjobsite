<?php
	session_start();
	if (!isset($_SESSION['email'])) {
		header("Location:index.php");
		exit();
	}
	include_once('inc/header.php');
	require_once('inc/connect.php');
?>

<?php
// If students registration or employer registration is complete, let them know their profile is ready for view
if($_GET['student_register'] == true || $_GET['employer_register'] == true)
{
	echo '<script type="text/javascript">
		$(document).ready(function () { 
			alert("Thank you for registering ' . $_SESSION['name'] . '! You may now view your profile.");
		})
		</script>';
}
?>

<div class="profilePage">
	<div class="leftSide">
		<div class="profileImage"></div>
		<div class="profileName">Company/Student Name</div>
		<div class="profileContact">Contact Info</div>
		<div class="profileAbout">
			Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
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
	if ($_SESSION['usertype']=='student') { 
		// Get the students skills
		$sql = "select * from student_skills where student_id=".$_SESSION['student_id']; 
		$result = mysql_query($sql) or die('fail1');;

		if ($row=mysql_fetch_array($result)) {
			$sql = "SELECT skill FROM skills WHERE skill_id=".$row['skill_id'];
			while($row=mysql_fetch_array($result)) {
				$sql = $sql." UNION SELECT skill FROM skills WHERE skill_id=".$row['skill_id'];
			}
			$result = mysql_query($sql) or die('fail2');


			echo "<div class='studentSkills'><div>Skills</div>\n";
			while($row=mysql_fetch_array($result)) {
				echo "<div class='skill threeCols'>".$row['skill']."</div>\n";
			}
			echo "</div>\n";
		}
	}
?>
	</div>
	<div class="rightSide">Jobs List
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
	</div>
</div>

<?php>
include_once('inc/footer.php');
?>
