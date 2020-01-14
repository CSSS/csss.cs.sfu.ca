<?php 
	@include('includes/sessions.inc.php'); 
	define('TITLE', 'Account');

	function changePassword(&$msg)
	{
		global $dbSession, $SESSION;
		if($_POST['oldpass'] == ''){
			// no old password
			$msg = "No old password entered!";
			return false;
		}
		if(!isset($_POST['newpass1']) || $_POST['newpass1'] == "")
		{
			// no new password
			$msg = "No new password entered!";
			return false;
		}
		if(!isset($_POST['newpass2']) || $_POST['newpass1'] != $_POST['newpass2'])
		{
			// passwords don't match
			$msg = "New passwords don't match!";
			return false;
		}
		if(strlen($_POST['newpass1']) < 8)
		{
			// password too short
			$msg = "New password is too short! (Minimum 8 characters)";
			return false;
		}
		// otherwise everything seems ok, check password
		$dbout = sendQuery($dbSession, "SELECT hash FROM logins WHERE username='{$SESSION['username']}';");
		$data = pg_fetch_object($dbout);
		if($data->hash != crypt($_POST['oldpass'], $data->hash))
		{
			// old password didn't match
			writeToLog($dbSession, $SESSION['username'], $SESSION['ip'], 'Failed to change password: incorrect old password provided.');
			$msg = "Old password is incorrect!";
			return false;
		}
		// old password is fine, new passwords are the same, time to compute the new password
		$newHash = hashPassword($_POST['newpass1']);
		if($newHash == '')
		{
			// error creating hash
			writeToLog($dbSession, $SESSION['username'], $SESSION['ip'], 'Failed to change password: hash failed!');
			$msg = "Failed to compute password hash.  Try again later.";
			return false;
		}
		// we have a hsash, time to update the db and return that we did it!
		$dbout = sendQuery($dbSession, "UPDATE logins SET hash='{$newHash}' WHERE ( username='{$SESSION['username']}');");
		writeToLog($dbSession, $SESSION['username'], $SESSION['ip'], 'Password changed successfully.');
		$msg = "Password changed successfully!";
		return true;
	}

	$passChange = false;
	$passChangePassed = false;
	$passChangeMessage = '';
	if(isset($_POST['oldpass']))
	{
		$passChange = true;
		$passChangePassed = changePassword($passChangeMessage); 
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<link rel="stylesheet" href="includes/style.css" type="text/css" />

<?php @include('includes/header.inc.php'); ?>

<div id='main'>
	<h1>Account Page</h1>
	<div id="loginForm">
		<h2>Change Password</h2>
		<form action="account.php" method="post"><p>
		<label for="oldpass">Old Password:</label><input type="password" name="oldpass" /><br />
		<label for="newpass1">New Password:</label><input type="password" name="newpass1" /><br />
		<label for="newpass2">Repeat Password:</label><input type="password" name="newpass2" /><input type="submit" />
		<?php if($passChange && !$passChangePassed) { echo "<div class='formError'>Change Failed: {$passChangeMessage}</div>"; } ?>
		<?php if($passChange && $passChangePassed) { echo "<div class='formSuccess'>{$passChangeMessage}</div>"; } ?>
		</p></form>
	</div>

</div>
<div id='sidebar'>
	<div class='box'>
		<div style="float:left;width:50%;"><h1>Account Info</h1></div><div style="float:right;width:50%;text-align:right;"><h1><a href='logout.php'>Log Out</a></h1></div>
		<?php
			echo '<p><b>'.htmlspecialchars($SESSION['username']).'</b>'; 
			if($SESSION['isAdmin'])
			{
				echo ' - <a href="admin.php">Administrator</a>';
			}else
			{
				echo ' - Member';
			}
			echo '</p>';
			if($SESSION['failedcount'] > 0)
			{
				$plural = ($SESSION['failedcount'] == 1 ? '' : 's');
				echo "<p><b>You've had {$SESSION['failedcount']} failed login{$plural} since your last session!</b></p>";
			}
			$lastDate = ($SESSION['lastlogin'] == 0 ? "Never" : date("d/m/Y @ g:ia", $SESSION['lastlogin']));
			echo "<p>Last login: $lastDate</p>";
		?>
	</div>
	<div class='box'>
		<h1><a href='createEvent.php'>Create new events</a></h1>
		<h1><a href='calendar.php'>Edit or delete old events</a></h1>
	</div>
</div>
<?php @include('includes/footer.inc.php'); ?>
