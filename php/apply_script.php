<?php
include_once("../inc/connect.php");
session_start();

// We dont want the header so include it once and it wont be included again in our email
ob_start();
include_once("../inc/header.php");
ob_end_clean();

// Start buffer and put student profile into it
ob_start();

// This links back to us from them
//$url = "http://".$_SERVER['SCRIPT_URI'];
$url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];
$url = str_replace("php/apply_script.php","",$url);

// The original header uses dynamic directories, we need to link back to our server from their email
?>
<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/2.9.0/build/reset-fonts-grids/reset-fonts-grids.css" />
		<link rel="stylesheet" type="text/css" href="<?php echo $url; ?>css/header.css" />
		<link rel="stylesheet" type="text/css" href="<?php echo $url; ?>css/jobs.css" />
		<link rel="icon" href="http://cit.dixie.edu/favicon.png"/>
		<link rel="shortcut icon" href="http://cit.dixie.edu/favicon.png"/>
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
		<script type="text/javascript" src="<?php echo $url; ?>js/jobs.js"></script>
		<title>CIT Job Board</title>
	</head>
	<body>
<?php
include_once("../profile.php");

// Grab the buffer as a string for emailing.
$page = ob_get_contents();
ob_end_clean();
$page .= "</body>\r\n</html>";

// Now that we have the page, we need to clean it up a little
// This regex removes all 'a' tags
$page = preg_replace('/(<a.+?)+(<\/a>)/i', '', $page); 
// Lets get rid of the matches box too. AKA chop the end off
$pos = strpos($page,'<div class="rightSide">');
$page = substr($page,0,$pos);
// If we have a resume, link to it
$sql = "SELECT resume FROM students WHERE email='$email'";
$result = mysql_query($sql) or die(mysql_error());
$row = mysql_fetch_assoc($result);
$res = &$row['resume'];
$resume = "";
if($res != '' && $res != "NULL")
	$resume = "<a href='".$url."resumes/$res'>Applicant Resume</a><br/>\r\n";

echo $page.$resume;

// Set up the email
$to = &$_POST['email'];
$title = &$_POST['title'];
$subject = "$title job applicant";

if(mail($to,$subject,$page))
	echo "=========success==========";
else
	echo "=========failure==========";

?>
