<?php
include_once("../inc/connect.php");
include_once("../inc/func.php");
session_start();

// This script dumps the profile page to an email when a student applies for a job
// We use buffers to get the profile page into a string. We then manipulate it.
// We have to put all the styles from the stylesheet inline for it to show up in email.
// We then MIME encode it and send it off. (phew)

// We dont want the header so include it once and it wont be included again in our email
ob_start();
include_once("../inc/header.php");
ob_end_clean();

// Start buffer and put student profile into it
ob_start();

// The original header opens a few tags
?>
<!DOCTYPE html>
<html>
	<body>
	<title>CIT Job Board</title>
<?php
include_once("../profile.php");

// Grab the buffer as a string for emailing.
$page = ob_get_contents();
ob_end_clean();

// Now that we have the page, we need to clean it up a little

// This regex removes all 'a' tags (mostly just the profile edit controls)
$page = preg_replace('/(<a.+?)+(<\/a>)/i', '', $page); 

// Lets get rid of the matches box too. AKA chop the end off
$pos = strpos($page,'<div class="rightSide">');
$page = substr($page,0,$pos);

// This hard codes with absolute links so they work in the email
//$url = "http://".$_SERVER['SCRIPT_URI'];
$url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];
$url = str_replace("/php/apply_script.php","",$url);

// If we have a resume, link to it
// Even if we have a link to it in profile, it will be removed by the link removal above
$sql = "SELECT resume,profile_pic FROM students WHERE email='$email'";
$result = mysql_query($sql) or die(mysql_error());
$row = mysql_fetch_assoc($result);
$res = &$row['resume'];
$resume = "";
if($res != '' && $res != "NULL") {
	$resume = "<a href='$url/resumes/$res'>Applicant Resume</a>";
	// Replace by more than just what we need in order to avoid replacing just "Resume" from everywhere
	$replace = '<div class="centerJustify">About me/Resume </div>';
	$with = "<div class='centerJustify'>About me/$resume</div>";
	$page = str_replace($replace,$with,$page);
}

// The profile pic isn't hard linked so we need to fix that
// For now, at least, the only image is going to be their pic. Lets take advantage of that.
$pic = &$row['profile_pic'];
$pic = "<img src='$url/profile_pics/$pic'>";
$page = preg_replace('/(<img.+?)+(>)/i', $pic, $page); 


// Close our tags
$page .= "</div></body></html>";

// HTML in emails requires that we use inline CSS
// This function call takes our page and css (strings) and returns the page after parsing them
$css = file_get_contents("../css/header.css");
$page = inline_styles($page, $css);

// There is one little tweak to the CSS to make.
// We dont have the right side so make the profile page width 100% rather than 69%
$page = str_replace("width: 69%", "width: 100%", $page);

// This is where the MIME stuff begins. I c/p'd this from the interwebz
$boundary = uniqid('np');

$headers = "MIME-Version: 1.0\r\n";
$headers .= "From: Taeler <twatkin2@dmail.dixie.edu>\r\n";
$headers .= "Subject: CIT Job test mail\r\n";
$headers .= "Content-Type: multipart/alternative;boundary=" . $boundary . "\r\n";

$message = "Job application notification."; 

$message .= "\r\n\r\n--" . $boundary . "\r\n";
$message .= "Content-type: text/plain;charset=utf-8\r\n\r\n";
$message .= "Please enable HTML to view this message.";

$message .= "\r\n\r\n--" . $boundary . "\r\n";
$message .= "Content-type: text/html;charset=utf-8\r\n\r\n";

// This is where our html page goes
$message .= $page;

$message .= "\r\n\r\n--" . $boundary . "--";


// Set up the email
$to = &$_POST['email'];
//$to = "twatkin2@dmail.dixie.edu";
//$to = '"Mark Whittaker" <mwhitta2@dmail.dixie.edu>, "Kasey Cowley" <kchunterdeluxe@gmail.com>, "Nick Cox" <nickcox1008@gmail.com>, "Steve Keeler" <stevekeeler057@gmail.com>, "Taeler Watkins" <twatkin2@dmail.dixie.edu>';
$title = &$_POST['title'];
$subject = "$title job applicant";

echo $message;
//mail($to, $subject, $message, $headers);
?>
