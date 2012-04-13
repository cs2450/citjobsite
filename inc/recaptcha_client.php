<?php
require_once('inc/recaptchalib.php');
$publickey = "6LdfJ9ASAAAAABj2OJD_1hsPE9AZByNijj14s6mL"; // you got this from the signup page
echo "<center>";
echo recaptcha_get_html($publickey);
echo "</center>";
?>
