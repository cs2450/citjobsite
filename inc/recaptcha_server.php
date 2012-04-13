<?php
require_once('recaptchalib.php');
$privatekey = "6LdfJ9ASAAAAAOB3HU0oQDfKspHooAB7NWi_5X9e";
$resp = recaptcha_check_answer ($privatekey,
		$_SERVER["REMOTE_ADDR"],
		$_POST["recaptcha_challenge_field"],
		$_POST["recaptcha_response_field"]);

if (!$resp->is_valid) {
	// What happens when the CAPTCHA was entered incorrectly
	$error="* Invalid recaptcha response";
}
?>
