<?php 
	@include('includes/sessions.inc.php'); 
	define('TITLE', 'Administration');
	
	if($SESSION['isAdmin'] == false){
		// not admin, kick out to account
		Header('Location: https://'.$_SERVER['HTTP_HOST'].'/account.php');
		exit();
	}

	$username = '';	
	$newUser = false;
	$newUserFailed = true;
	$newUserMessage = '';
	if(isset($_POST['username']))
	{
		$newUser = true;
		$newUserFailed = false;
		$username = pg_escape_string($_POST['username']);
		if($username == ''){
			// no username
			$newUserMessage = "No username entered!";
			$newUserFailed = true;
		}elseif($_POST['newpass1'] == "")
		{
			// no new password
			$newUserMessage = "No password entered!";
			$newUserFailed = true;
		}elseif($_POST['newpass1'] != $_POST['newpass2'])
		{
			// passwords don't match
			$newUserMessage = "Passwords don't match!";
			$newUserFailed = true;
		}elseif(strlen($_POST['newpass1']) < 8)
		{
			// password too short
			$newUserMessage = "Password is too short! (Minimum 8 characters)";
			$newUserFailed = true;
		}else
		{			
			// otherwise everything seems ok, check user
			$dbout = sendQuery($dbSession, "SELECT COUNT(username) AS number FROM logins WHERE username='{$username}';");
			$data = pg_fetch_object($dbout);
			if($data->number != 0)
			{
				// user already exists
				$newUserMessage = "User already exists!";
				$newUserFailed = true;
			}else
			{
				// time to make a new user
				$newHash = hashPassword($_POST['newpass1']);
				if($newHash == '')
				{
					// error creating hash
					writeToLog($dbSession, $SESSION['username'], $SESSION['ip'], 'Failed to create new user: hash failed!');
					$newUserMessage = "Failed to compute password hash.  Try again later.";
					$newUserFailed = true;
				}else{
					// we have a hash, time to update the db and return that we did it!
					$dbout = sendQuery($dbSession, "INSERT INTO logins (username, hash, failedcount, lastlogin, isadmin) VALUES ('{$username}', '{$newHash}', 0, 0, FALSE );");
					writeToLog($dbSession, $SESSION['username'], $SESSION['ip'], "Created new user '{$username}'.");
					$newUserMessage = "User created successfully!";
				}
			}
		}
	}



	$deleteUser = false;
	$deleteUserPassed = false;
	$deleteUserMessage = '';

	if(isset($_POST['deluser']))
	{
		$deleteUser = true;
		$tempUser = pg_escape_string($_POST['deluser']);
		$htmlUser = htmlspecialchars($tempUser);
		$tempQuery = sendQuery($dbSession, "SELECT isadmin FROM logins WHERE username='$tempUser';");
		$data = pg_fetch_object($tempQuery);
		if(pg_num_rows($tempQuery) == 1)
		{
			// user does exist
			if($data->isadmin != 't')
			{
				// user isn't admin
				$deleteUserPassed = true;
				$deleteUserMessage = "$htmlUser has been deleted!";
				writeToLog($dbSession, $SESSION['username'], $SESSION['ip'], "Deleted user '$tempUser'.");
				sendQuery($dbSession, "DELETE FROM logins WHERE username='$tempUser';");
				// and get rid of any sessions they owned
				sendQuery($dbSession, "DELETE FROM sessions WHERE username='$tempUser';");

			}else
			{
				// user is admin
				$deleteUserMessage = "$htmlUser is an administrator!";
				writeToLog($dbSession, $SESSION['username'], $SESSION['ip'], "Tried to delete administrator '$tempUser'!");
			}
		}else
		{
			// user doesn't exist
			$deleteUserMessage = "$htmlUser doesn't exists!";
			writeToLog($dbSession, $SESSION['username'], $SESSION['ip'], "Tried to delete '$tempUser' who doesn't exist!");
		}
	}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<link rel="stylesheet" href="includes/style.css" type="text/css" />
	<script src='//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js' type="text/javascript"></script>
	<script type="text/javascript">
		$(function() {
			$(':submit').click(function(e){
				var name = $(this).attr('name');
				if(name != undefined)
				{
					var ok = confirm("Do you really want to delete "+name+"?");
					if(ok)
					{
						return true;
					}else
					{
						e.preventDefault();
						return false;
					}
				}else
				{
					return true;
				}
			});


		});
	</script>

<?php @include('includes/header.inc.php'); ?>

<div id='main'>
	<h1>Admin Page</h1>
	<div id="loginForm">
		<h2>Create New User</h2>
		<form action='admin.php' method='post'><p>
		<label>User Name:</label><input type="text" name="username" value="<?php echo $username; ?>" /><br />
		<label>Password:</label><input type="password" name="newpass1" /><br />
		<label>Repeat Password:</label><input type="password" name="newpass2" /><input type="submit" />
		<?php if($newUser && $newUserFailed){ echo "<div class='formError'>Creation Failed: ".$newUserMessage."</div>"; } ?>
		<?php if($newUser && !$newUserFailed){ echo "<div class='formSuccess'>".$newUserMessage."</div>";} ?>
		</p></form>
	</div>
	<div id="userTable">
	<h2>All Users</h2>
	<table><tr><th>User Name</th><th>Last Login</th><th>Admin</th><th>Delete</th></tr>
	<?php // select all users
	$userQuery = sendQuery($dbSession, "SELECT username,lastlogin,isadmin FROM logins;");
	while($data = pg_fetch_object($userQuery))
	{
		$cleanName = htmlspecialchars($data->username);
		$tempDate = ($data->lastlogin != 0 ? date("d/m/Y - h:i:sa", $data->lastlogin) : "Never");
		$isAdmin = ($data->isadmin == 't' ? "True" : "False");
		$deleteURL = ($data->isadmin == 't' ? "<div><input type='submit' value='Delete' disabled='disabled'/></div>" : "<form action='admin.php' method='post'><div><input type='hidden' name='deluser' value='$data->username'/><input name='$data->username' type='submit' value='Delete'/></div></form>");
		echo "<tr><td>$cleanName</td><td>$tempDate</td><td>$isAdmin</td><td>$deleteURL</td></tr>";
	}
	echo '</table>'; 
	if($deleteUser && !$deleteUserPassed){ echo "<div class='formError'>Delete Failed: $deleteUserMessage</div>"; }
	if($deleteUser && $deleteUserPassed){ echo "<div class='formSuccess'>$deleteUserMessage</div>"; }
	?>
	</div>
</div>
<div id='sidebar'>
	<div class='box'>
		<h1>Active Sessions</h1>
		<p><?php
		$firstSession = True;
		$sessionQuery = sendQuery($dbSession, "SELECT username,time FROM sessions;");
		while($data = pg_fetch_object($sessionQuery))
		{
			$cleanName = htmlspecialchars($data->username);
			$tempData = date("h:i:sa", $data->time);
			$comma = ($firstSession ? '' : ', ');
			echo "$comma<span title='$tempData'><b>$cleanName</b></span>";
			$firstSession = False;
		}?></p>
	</div>
	<div class='box'>
		<div style='float:left;width:50%;'><h1>Site Log</h1></div><div style='float:right;width:50%;text-align:right;'><h1><a href='sitelog.php'>View All</a></h1></div>
		<?php
		$logQuery = sendQuery($dbSession, "SELECT username,microtime,ip,message FROM sitelog ORDER BY microtime DESC LIMIT 5;");
		while($data = pg_fetch_object($logQuery))
		{
			list($logTime, $logMs) = explode(' ', $data->microtime);
			echo '<p><span title="IP: '.htmlspecialchars($data->ip).'">'.htmlspecialchars($data->message).'<br /><small>'.htmlspecialchars($data->username).' - '.date('d/m/Y @ h:i:sa', $logTime).'</small></span><br /></p>';
		}?>
	</div>
</div>
<?php @include('includes/footer.inc.php'); ?>
