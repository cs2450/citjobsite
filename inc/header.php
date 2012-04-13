<!DOCTYPE html>
<?php
// Satisfies all of 9.0 login page specifications
session_start();
//require_once('../includes/config.php');
//require_once('../includes/functions.php');
//myheader('CIT Jobs');

require_once('connect.php');
?>
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/2.9.0/build/reset-fonts-grids/reset-fonts-grids.css" />
		<link rel="stylesheet" type="text/css" href="./css/header.css" />
        <link rel="stylesheet" type="text/css" href="./css/jobs.css" />
		<link rel="icon" href="http://cit.dixie.edu/favicon.png"/>
		<link rel="shortcut icon" href="http://cit.dixie.edu/favicon.png"/>
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
		<script type="text/javascript" src="js/jobs.js"></script>
		<title>CIT Job Board</title>
	</head>
	<body>
		<div id="citNav"><img src="images/bannerMain.png"></img></div>
		<div id="sticky-anchor"></div>
<?php	if (isset($_SESSION['email'])) { 
	// The matches button changes to 'Post Job' if we are an employer
	if ($_SESSION['user_type'] == 'student' && isset($_GET['page']) && $_GET['page']=='Jobs')
		$match_button = "All Jobs";
	else if($_SESSION['user_type']=='employer')
		$match_button = "Post Job";
	else
		$match_button = "All Jobs";
	?>
		<div id="menuBox" class="regColors sticky-handle">
			<form id="loginForm" class="regColors" method="post" action="php/login_script.php">
				<div class="formButton threeCols">
					<button type="submit" name="home" value="home" id="homeButton"
<?php if (isset($_GET['page']) && $_GET['page']=='home') echo ' class="active"' ?>></button>
				</div>
				<div class="formButton threeCols">
					<button type="submit" name="<?php echo $match_button; ?>" value="<?php echo $match_button; ?>" id="matchesButton" <?php if (isset($_GET['page']) && in_array($_GET['page'],array("Jobs","Matches"))) echo ' class="active"' ?>></button>
				</div>
				<div class="formButton threeCols">
					<button type="submit" name="logout" value="logout" id="logoutButton"<?php if (isset($_GET['page']) && $_GET['page']=='logout') echo 'class="active"' ?>></button>
				</div>
				<div class="buttonText threeCols">
					<label for="homeButton">Home</label>
				</div>
				<div class="buttonText threeCols">
					<label for="matchesButton"><?php echo $match_button; ?></label>
				</div>
				<div class="buttonText threeCols">
					<label for="logoutButton">Log-Out</label>
				</div>
			</form>
<?php	} else { ?>
		<div id="loginBox" class="regColors sticky-handle">
			<form id="loginForm" class="regColors" method="post" action="php/login_script.php">
				<div class="formLeft">
					<div class="formItem">
						<label for="email">Email:</label>
					</div>
					<div class="formItem">
						<label for="password">Password:</label>
					</div>
				</div>
				<div class="formRight">
						<input type="text" name="email" size="20" id="email"></input>
						<input type="password" name="password" id="password"></input>
				</div>
				<div class="formBottom">
					<div class="formButton threeCols">
						<button type="submit" name="login" value="login" id="loginButton"></button>
					</div>
					<div class="formButton threeCols">
						<button type="submit" name="register" value="register" id="registerButton"></button>
					</div>
					<div class="formButton threeCols">
						<button type="submit" name="index" value="index" id="indexButton" <?php if (isset($_GET['page']) && $_GET['page']=='home') echo ' class="active"' ?>></button>
					</div>
					<div class="buttonText threeCols">
						<label for="loginButton">Log-In</label>
					</div>
					<div class="buttonText threeCols">
						<label for="registerButton">Register</label>
					</div>
					<div class="buttonText threeCols">
						<label for="indexButton">Jobs List</label>
					</div>
				</div>
			</form>
<?php	} include_once("inc/sitemap.php");  ?>
		</div>
		<div id="mainBody" class="regColors">
