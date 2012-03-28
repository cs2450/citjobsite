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

function validate($user, $pass, $type){
  if($user != "" and $pass != "") {
		if($type == 'employer')
		{
			$sql = "SELECT * FROM employers WHERE email='$user' AND password='$pass'";
			$result = mysql_query($sql) or die(mysql_error());
			$queryrows = mysql_num_rows($result);
			
			if($queryrows > 0) {
			  srand(time());
			  $SecurityHash = "";
			  
			  for($i = 0; $i < 32; $i++)
				$SecurityHash .= (string)rand(0, 9);
				
			  $row=mysql_fetch_array($result);
			  $_SESSION['name'] = $row['name'];
			  $_SESSION['company'] = $row['company'];
			  $_SESSION['email'] = $user;          
			  $_SESSION['phone'] = $row['phone'];
			  $_SESSION['access_level']=$row['access'];
			  $_SESSION['Security_Hash'] = $SecurityHash;
			  $_SESSION['Security_TokenMd5'] = md5($SecurityHash . "This Is not For you to see");
			  $_SESSION['user_type'] = $type;
			  
			  return true;
			 }
			  
			else {
				return false;
			}
		}
		
		else if($type == 'student')
		{
			$sql = "SELECT * FROM students WHERE email='$user' AND password='$pass'";
			$result = mysql_query($sql) or die(mysql_error());
			$queryrows = mysql_num_rows($result);
			
			if($queryrows > 0) {
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
?>
