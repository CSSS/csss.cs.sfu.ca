<?php
	include("includes/sessions.inc.php");
	define('TITLE', 'Login');

	date_default_timezone_set("America/Vancouver"); 
	
	$attemptedLogin = false;
	$reason = "Username or Password doesn't match.";
	$username='';
	
	// read in username and password, if it exists
	if( isset($_POST['username']) && $_POST['username'] != "")
	{
		$attemptedLogin = true;
		// we have a post input, lets process it
		//$dbmakeLoginResult = sendQuery($dbconn, "CREATE TABLE IF NOT EXISTS logins (username varchar(255), hash varchar(2047), failedcount int8, lastlogin int8, isadmin bool, PRIMARY KEY(username));");
		//$dbmakeLogResult = sendQuery($dbconn,  "CREATE TABLE IF NOT EXISTS sitelog (username varchar(255), timestamp int8, microtime varchar(255), ip varchar(1023), message varchar(65534), PRIMARY KEY(username, timestamp, microtime));");
			
		$username = pg_escape_string($_POST['username']);

		$dbresult = sendQuery($dbSession, "SELECT hash,lastlogin,failedcount,isadmin FROM logins WHERE ( username='{$username}' );");
		// compare password
		if(pg_num_rows($dbresult) == 0)
		{
			// username doesn't exist
			// should delay for a small time since password processing takes awhile and we don't want them to know they don't have a username
			crypt("nothingofimportance", '$6$rounds=100000$somestupidstuffthatdoesnothing$');
			$loginFailed = true;
			writeToLog($dbSession, "__loginuser", $SESSION['ip'], "Attemped login with invalid username: ".$username);
		}else
		{

			$data = pg_fetch_object($dbresult);
			if($data->hash == crypt($_POST['password'], $data->hash))
			{
				// good password, generate a session
				$SESSION['hash'] = getSessionID();
				$SESSION['username'] = $username;
				$SESSION['isAdmin'] = ($data->isadmin == 't' ? True : False);
				$SESSION['lastlogin'] = $data->lastlogin;
				$SESSION['failedcount'] = $data->failedcount;
				sendQuery($dbSession, "DELETE FROM sessions WHERE username='{$SESSION['username']}';"); // drop old sessions
				sendQuery($dbSession, "INSERT INTO sessions (username, hash, ip, time, data) VALUES ('{$SESSION['username']}', '{$SESSION['hash']}', '{$SESSION['ip']}', $currentTime, '');"); // add new session
				sendQuery($dbSession, "UPDATE logins SET lastlogin=$currentTime, failedCount=0 WHERE username='{$SESSION['username']}';"); // update login info
				writeToLog($dbSession, $username, $SESSION['ip'], 'Login successful.');
				updateSessionData(); // write $SESSION variable to table

				// send their cookie
				setcookie('session', $SESSION['hash'], 0, '/', '', True, True);

				// check if they have a redirect
				if(!empty($_COOKIE['redirect']))
				{
					$redirect = $_COOKIE['redirect'];
					setcookie('redirect', '', 0, '/', '', True, True); // clear the cookie
					header('Location: https://'.$_SERVER['HTTP_HOST'].$redirect);
				}else // otherwise send them to the account page
				{
					header('Location: https://'.$_SERVER['HTTP_HOST'].'/account.php');
				}
				exit();
			}else
			{
				// bad password, increment failed counter
				sendQuery($dbSession, "UPDATE logins SET failedcount=failedcount+1 WHERE ( username='{$username}' );");
				// most likely want to do more stuff here
				writeToLog($dbSession, $username, $SESSION['ip'], 'Login failed with invalid password.');
			}
		}
	}
			
	
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
	<div id="loginForm">
	<h1>CSSS Website Login</h1>
	<form action="login.php" method="post"><p>
	<label>User Name:</label><input type="text" name="username" value="<?php echo $username; ?>" /><br />
	<label>Password:</label><input type="password" name="password" /><input type="submit" />
	<br style='clear:both;' />
	<?php if($attemptedLogin) { echo "<div class='formError'>Login Failed: ".$reason."</div>"; } ?>
	</p></form>
	</div>
	</div>
	<?php
	@include('includes/footer.inc.php');
	?>
