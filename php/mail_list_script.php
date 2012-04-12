<?php
// Dynamic url yay!
$url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];
$url = str_replace("/php/post_job_script.php","",$url);

$message = "A new job has been posted at $url/job_detail?job=$job_id";
$subject = "New job posted: ".$_POST['title'];

$headers = "From: CIT Job Board <citjobs@dixie.edu>\r\n";
$headers .="Subject: New job posted\r\n";

$sql = "SELECT * FROM mail_list";
$result = mysql_query($sql) or die(mysql_error());
// kick start the loop with the first entry
if(mysql_num_rows($result) > 0){
	$row = mysql_fetch_assoc($result);
	$headers .= "BCC: ".$row['email'];
	while($row = mysql_fetch_assoc($result)) { 
		$headers .= ", ".$row['email'];
	}
}
mail(null, $subject, $message, $headers);
?>
