<?php
	include('includes/sessions.inc.php');

	date_default_timezone_set("America/Vancouver");

	// if we are at this page, time to logout
	sendQuery($dbSession, "DELETE FROM sessions WHERE hash='{$SESSION['hash']}';");

	writeToLog($dbSession, $SESSION['username'], $SESSION['ip'], 'Logged out successfully.');
	// delete their cookie
	setCookie('session', '', 0, '/', '', True, True);
	
	define('TITLE', 'Logout');

	?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns='http://www.w3.org/1999/xhtml'>
	<head>

	<link rel='stylesheet' href='includes/style.css' type='text/css' />
	
	<?php
	
	@include('includes/header.inc.php');
	
	?>
	<div id="content">
	<h1>Log Out</h1>
	<p>You have successfully logged out!</p>
	<p><a href='index.php'>Return to the home page</a></p>
	</div>
	<?php @include('includes/footer.inc.php');?>

