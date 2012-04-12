<?php
//
// Satisfies specification 2.4 -mw 2/29/12
//

function validate_data($name,$email,$con_email,$phone,$pass,$con_passwd){
  $regexp="/^[a-z0-9]+([_\\.-][a-z0-9]+)*@([a-z0-9]+([\.-][a-z0-9]+)*)+\\.[a-z]{2,}$/i";
  $error='';

  if($name=="" or $phone=="" or $email=="" or $pass=="" or $con_passwd=="") {
    $error="* Please fill out all fields";
  }
  else if ( !preg_match($regexp, $email) ) {
    $error="* Please enter a valid e-mail address";
  }
  else if($pass != $con_passwd){
    $error="* Passwords do not match";
  }
  else if(strlen($pass) < 6){
    $error="* Password needs to be at least 6 characters";
  }
  
  return $error; 
}

//
// This function generates the skills checkboxes on skills.php. Whenever a new skill is added to the database, a new checkbox is created in skills.php.
// Specification covered: 3.1.1 (Divide skills by CIT emphasis)

// current_skills is an array filled by querying for selected skills
// filled using looped: $current_skills[$row['skill_id']] = true;
// from post_job.php it is filled using:
// $current_skills[$row['skill_id']] = $row['match_priority'];
// Keep in mind that match_priority includes 0's
function query($dept,$current_skills,$form_type='student') 
{
	// Select the skills from the cs department
	$sql = 'SELECT skill_id, skill FROM skills WHERE dept="'.$dept.'"';
	$result = mysql_query($sql) or die ('Cannot query database');
	while($row = mysql_fetch_assoc($result))
	{
		$skill = $row['skill'];
		$checked = "";
		if (isset($current_skills[$row['skill_id']])) {
			$checked = "checked";
		}
		if ($form_type == 'job') {
			// Matching selector
			$op1='';
			$op2='';
			if ($current_skills[$row['skill_id']] == 1)
				$op1 = 'Selected';
			else if ($current_skills[$row['skill_id']] == 2)
				$op2 = 'Selected';
			echo "<select name='match_".$row['skill_id']."'>\n<option value='0'>None</optoin>\n<option value='1' $op1>Required</option>\n<option value='2' $op2>Desirable</option>";
			// Skill checkbox
			echo '<input type="checkbox" name="'.$row['skill_id'].'" '.$checked.' /> <label for="'.$skill.'">'.$skill.'</label><br />';
		}
		else {
			echo '<input type="checkbox" name="'.$row['skill_id'].'" '.$checked.' /> <label for="'.$skill.'">'.$skill.'</label><br />';
		}
	}
}


// This function sets the session variables and validates the specific user. Function requires a username, a password, and the type of person logging in.
// Selects records from the database and sets the session variables upon successful validation.

function validate($user, $pass, $type, &$error=NULL){
  if($user != "" and $pass != "") {
		if($type == 'employer')
		{
			$sql = "SELECT * FROM employers WHERE email='$user' AND password=PASSWORD('$pass')";
			$result = mysql_query($sql) or die(mysql_error());
			$queryrows = mysql_num_rows($result);
			
			if($queryrows > 0) {
			  $row=mysql_fetch_array($result);
			  // Don't allow suspended accounts to login
			  if($row['access'] == -1){
				  $error = 'account_suspended';
				  return false;
			  }
			  srand(time());
			  $SecurityHash = "";
			  
			  for($i = 0; $i < 32; $i++)
				$SecurityHash .= (string)rand(0, 9);
				
			  $_SESSION['name'] = $row['name'];
			  $_SESSION['company'] = $row['company'];
			  $_SESSION['email'] = $user;          
			  $_SESSION['phone'] = $row['phone'];
			  $_SESSION['access_level']=$row['access'];
			  $_SESSION['Security_Hash'] = $SecurityHash;
			  $_SESSION['Security_TokenMd5'] = md5($SecurityHash . "This Is not For you to see");
			  if($row['access']==5)
				  $_SESSION['user_type'] = 'admin';
			  else
			  	  $_SESSION['user_type'] = $type;
			  
			  return true;
			 }
			  
			else {
				return false;
			}
		}
		
		else if($type == 'student')
		{
			$sql = "SELECT * FROM students WHERE email='$user' AND password=PASSWORD('$pass')";
			$result = mysql_query($sql) or die(mysql_error());
			$queryrows = mysql_num_rows($result);
			if($queryrows == 1) {
			  srand(time());
			  $SecurityHash = "";
			  
			  for($i = 0; $i < 32; $i++)
				$SecurityHash .= (string)rand(0, 9);
				
			  $row=mysql_fetch_array($result);
			  $_SESSION['student_id'] = $row['studentID'];
			  $_SESSION['name'] = $row['name'];
			  $_SESSION['email'] = $row['email'];          
			  $_SESSION['Security_Hash'] = $SecurityHash;
			  $_SESSION['Security_TokenMd5'] = md5($SecurityHash . "This Is not For you to see");
			  $_SESSION['user_type'] = $type;
			  
			  return true;
			 }
			  
			else {
				return false;
			}
		}
    }
	
    return false;
}

// This function calls the match functions and returns the SQL statement
function fetch_matches($id,$pagelimit,$offset) {
	$matches = begin_match($id);
	$size = sizeof($matches);
	// Lets get a maximum of $pagelimit
	if($size > 0) {
		$job_id = key($matches);
		next($matches);
		$sql="SELECT * FROM jobs WHERE id='$job_id'";
		if($size>$pagelimit)
			$size = $pagelimit;
		for($i=1;$i<$size;$i++)
		{	
			$job_id = key($matches);
			$sql .= " UNION SELECT * FROM jobs WHERE id='$job_id'";
			next($matches);
		}
		// Finish up by adding the the page offset, if any
		$sql .= " LIMIT $pagelimit OFFSET $offset";
	}
	// No matches? Just display the normal job list.
	else
		$sql="SELECT * FROM jobs WHERE status='active' ORDER BY date DESC LIMIT $pagelimit OFFSET $offset";
	
	return $sql;
}

// This is a helper function for the matching it gathers the student's skills into an array
// It then calls the main match function and returns the matched jobs
function begin_match($id) {
	$sql = "SELECT skill_id FROM student_skills WHERE student_id='$id'";
	$result= mysql_query($sql) or die("failed fetching skills");
	$current_skills = array();
	while ($row=mysql_fetch_array($result)) {
		    if($row['skill_id'] != 0)
				        array_push($current_skills,$row['skill_id']);
	}                                                                   
	return match($current_skills);
}

// This is the matching function.
// It takes an array of student skills (ids) and returns an array[job_id] = matchRating
// sorted by the rating.
function match($student_skills) {
	$sql = "SELECT * from job_skills WHERE active=1";
	$result = mysql_query($sql) or die(mysql_error());
	$final = array();
	$jobMatchCount = 0;
	$matchCount = 0;
	$unqualified = false;
	$row = mysql_fetch_array($result);
	$lastID = $row['job_id'];
	do {
		// If we are on a new job, calculate and store the info and start on the next job
		if($row['job_id'] != $lastID) {
			// Don't count jobs for which student is unqualified or jobs that have no skills set for matching
			if(!$unqualified && $jobMatchCount != 0) {
				$percent = 1.0*$matchCount/$jobMatchCount;
				$final[$lastID] = $percent;
			}
			$matchCount = 0;
			$jobMatchCount = 0;
			$unqualified = false;
		}
		$lastID = $row['job_id'];
		// Don't keep checking if they are unqualified for this job
		if (!$unqualified) {
			// If this job's skill has matched priority, inc our counter
			if($row['match_priority'] == 2)
				$jobMatchCount += 1;
			// If student's skills match this one and it is a priority skill, inc the student's counter
			if($row['match_priority'] == 2 && in_array($row['skill_id'], $student_skills))
				$matchCount += 1;
			// If this is a required skill that the student does not have, mark them unqualified until the next job
			if($row['match_priority'] == 1 && !in_array($row['skill_id'], $student_skills))
				$unqualified = true;
		}
	} while($row = mysql_fetch_array($result));

	// Due to poor loop planning, I need to repeat this again :(
			// Don't count jobs for which student is unqualified or jobs that have no skills set for matching
			if(!$unqualified && $jobMatchCount != 0) {
				$percent = 1.0*$matchCount/$jobMatchCount;
				$final[$lastID] = $percent;
			}
			$matchCount = 0;
			$jobMatchCount = 0;
			$unqualified = false;

	// Now we want to sort the array first by skill percentage.
	arsort($final);
	return $final;
}

// This function handles file uploads of resumes and profile pics for students and employers

function handleFileUpload($file, $type)
{
	// Get file extension
		$ext = substr($file['name'], strripos($file['name'], '.'));
		$ext = strtolower($ext);
		
		$error = '';

		if($type == 'profile' || $type == 'logo')
		{
			if($ext != '.jpg' && $ext != '.jpeg' && $ext != '.gif' && $ext != '.png')
			{
				$error = 'extension&type=image';
			}
		}
		
		// if it is a resume
		if($type == 'resume')
		{
			if($ext != '.pdf')
			{
				$error = 'extension&type=resume';
			}

			else if($file['size'] > 5000000)
			{
				$error = 'size';
			}
		}
		
		// If an error is set, redirect to edit_company.php with an error
		if($error != '' && $type == 'logo')
		{
			header("Location:../edit_company.php?error=".$error);
		}

		else if($error != '' && ($type == 'profile' || $type == 'resume'))
		{
			header("Location:../resume.php?error=".$error);
		}

		// Else, upload the file
		else
		{
			$document_name = $_SESSION['email'] . $ext;
			$directory = '';
			
			if($type == 'logo')
			{
				$company = $_SESSION['company'];
				$email = $_SESSION['email'];
				$directory = "logos/";
		
				// Check the database to see if there is already a logo. If there is,
				// go to the directory and delete the previous logo to make room for
				// the new one
				$sql = "SELECT logo FROM employers WHERE email='$email'";
				$result = mysql_query($sql) or die("Cannot query database: " . mysql_error());
				if(mysql_num_rows($result) == 1)
				{
					$row = mysql_fetch_assoc($result);
					if($row['logo'] != NULL)
					{
						if(chdir($directory))
						{
							$directory = '';
							$delete_file = @unlink($row['logo']);
						}
					}
				}
			}
			
			else if($type == 'profile')
			{
				$email = $_SESSION['email'];
				$directory = "profile_pics/";
			
				// Check the database to see if there is already a resume. If there is,
				// go to the directory and delete the previous resume to make room for
				// the new one
				$sql = "SELECT profile_pic FROM students WHERE email='$email'";
				$result = mysql_query($sql) or die("Cannot query database: " . mysql_error());
				if(mysql_num_rows($result) == 1)
				{
					$row = mysql_fetch_assoc($result);
					if($row['profile_pic'] != NULL)
					{
						if(chdir($directory))
						{
							$directory = '';
							$delete_file = @unlink($row['profile_pic']);
						}
					}
				}
			}
			
			else if($type == 'resume')
			{
				$email = $_SESSION['email'];
				$directory = "resumes/";
				
				// Check the database to see if there is already a resume. If there is,
				// go to the directory and delete the previous resume to make room for
				// the new one
				$sql = "SELECT resume FROM students WHERE email='$email'";
				$result = mysql_query($sql) or die("Cannot query database: " . mysql_error());
				if(mysql_num_rows($result) == 1)
				{
					$row = mysql_fetch_assoc($result);
					if($row['resume'] != NULL)
					{
						if(chdir($directory))
						{
							$directory = '';
							$delete_file = @unlink($row['resume']);
						}
					}
				}
			}

			// Move temporary file to resumes 
			if(!move_uploaded_file($file['tmp_name'], "$directory$document_name"))
			{
				if($type == 'logo')
					header("Location:../edit_company.php?error=move_fail");
				else if($type == 'profile' || $type == 'resume')
					header("Location:../resume.php?error=move_fail");
			}
			
			else
			{
				chmod("$directory$document_name", 0744);
			}
		}
	
	// Once everything has been handled properly, update the mysql database
	if($type == 'resume')
	{
		$sql = "UPDATE students SET resume='$document_name' WHERE email='$email'";
	}
	
	else if($type == 'profile')
	{
		$sql = "UPDATE students SET profile_pic='$document_name' WHERE email='$email'";
	}
	
	else if($type == 'logo')
	{
		$sql = "UPDATE employers SET logo='$document_name' WHERE email='$email'";
	}

	mysql_query($sql) or die("Cannot query database: " . mysql_error());
}

// This function detects class="foo" and replaces it with the inlined styles
// Works with both class="foo" AND class='foo'
function inline_styles($page, $css) {
	// What we are looking for
	$findme = "class=";
	$length = strlen($findme);

	$classloc = strpos($page,$findme);

	// While there is a class to be replaced...
	while($classloc !== false) {
		$start = $classloc+$length;
		// Store what the next char is (' or ")
		$quote = $page[$start];
		// Find the one on the other end
		$endquote = strpos($page, $quote, $start+1);
		// This will be our class(es)
		$inside = substr($page, $start+1, $endquote-$start-1);
		$classes = explode(" ", $inside);
		$inline = "";
		// For each class, find it in the css, chop it up (as above but with {}), and place into string
		// In hindsight, I should have parsed the css into a nice array first... these will get sliced multiple times
		foreach($classes as $class) {
			$cssloc = strpos($css, $class);
			if($cssloc !== false) {
				$lbrace = strpos($css, '{', $cssloc);
				$rbrace = strpos($css, '}', $cssloc);
				$inline .= substr($css, $lbrace+1, $rbrace-$lbrace-1);
			}
		}
		// Tack on the before and after
		$inline = "style='$inline'";
		// Clean off any newlines and tabs
		$junk = array("\n","\r","\t");
		$inline = str_replace($junk, "", $inline);
		// Finally, we put it where it goes
		$replace = substr($page, $classloc, $endquote-$classloc-1);
		$page = str_replace($replace, $inline, $page);

		$classloc = strpos($page,$findme, $classloc+1);
	}
	return $page;
}

?>
