<?php
	@include('passwords.priv');
	// redirect to https if not https
	if($_SERVER['HTTPS'] != 'on')
	{
		header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
		exit();
	}

	date_default_timezone_set("America/Vancouver");
	

	$dbSession = connectToSQL("host={$sql_host} dbname={$sql_database} user={$sql_user} password={$sql_password}");
	$currentTime = time();
	$SESSION = array();
	$sessionTimeout = 60*60; // 1 hour
	//sendQuery($dbSession, "CREATE TABLE IF NOT EXISTS sessions (username varchar(255), hash varchar(255), ip varchar(255), time int8, data varchar(8191));")
	;
	// delete old sessions
	$expireTime = $currentTime-$sessionTimeout;
	sendQuery($dbSession, "DELETE FROM sessions WHERE time<{$expireTime};");

	// check the cookie
	$SESSION['hash'] = '';
	if(isset($_COOKIE['session']))
	{
		$SESSION['hash'] = pg_escape_string($_COOKIE['session']);
	}
	// get the session from the database

	$SESSION['ip'] = pg_escape_string($_SERVER['REMOTE_ADDR']);
	
	$sessionResult = sendQuery($dbSession, "SELECT * FROM sessions WHERE hash='{$SESSION['hash']}' AND ip='{$SESSION['ip']}';");
	if(pg_num_rows($sessionResult) != 0 && $SESSION['hash'] != '')
	{
		// still a valid session
		$sessionObject = pg_fetch_object($sessionResult);
		$SESSION['username'] = pg_escape_string($sessionObject->username);
		// get data from database
		$tempData = unserialize($sessionObject->data);
		if(!is_array($tempData))
		{
			$tempData = array();
		}
		$SESSION = array_merge($SESSION, $tempData);
		// update our new time
		sendQuery($dbSession, "UPDATE sessions SET time={$currentTime} WHERE hash='{$SESSION['hash']}';");
		if(empty($SESSION['username'])) // this shouldn't ever happen
		{
			// invalidate session and send to login page
			sendQuery($dbSession, "DELETE FROM sessions WHERE hash='{$SESSION['hash']}';");
			setcookie('session', '', 0, '/', '', True, True);
			header('Location: https://'.$_SERVER['HTTP_HOST'].'/login.php');
			exit();
		}
		// if on login page, kick out to account
		if(!(strpos($_SERVER['PHP_SELF'], 'login.php') === FALSE))
		{
			header('Location: https://'.$_SERVER['HTTP_HOST'].'/account.php');
			exit();
		}
	}else
	{
		// doesn't have a session, so redirect to login page
		if(strpos($_SERVER['PHP_SELF'], 'login.php') === FALSE)
		{
			if($_SERVER['REQUEST_URI'] != '')
			{
				setcookie('redirect', $_SERVER['REQUEST_URI'], 0, '/', '', True, True);
			}
			// redirect to login
			header('Location: https://'.$_SERVER['HTTP_HOST'].'/login.php');
			exit();
		}
	}

	function connectToSQL(){
		global $sql_host, $sql_database, $sql_user, $sql_password;
		$out = pg_connect("host={$sql_host} dbname={$sql_database} user={$sql_user} password={$sql_password}");
		if($out == null){
			die("<h2>Error: Failed to connect to the database!</h2>");
		}
		return $out;
	}
	
	function sendQuery($conn, $query){
		$out = pg_query($conn, $query);
		if($out == null)
		{
			die("<h2>Error: Database query failed!</h2>");
		}
		return $out;
	}
	
	function writeToLog($conn, $user, $ipaddress, $message){
		$microTime = microtime();
		list($u, $s) = explode(' ', $microTime);
		$tempTime = $s.' '.$u;
		$msg = pg_escape_string($message);
		sendQuery($conn, "INSERT INTO sitelog (username, microtime, ip, message) VALUES ('{$user}', '{$tempTime}', '{$ipaddress}', '{$msg}');");
	}

	function getUUID($length)
	{
		$pr_bits = '';
		$fp = @fopen('/proc/sys/kernel/random/uuid','r');
		if ($fp !== FALSE) {
			$pr_bits .= @fread($fp,$length);
			@fclose($fp);
		}
		return $pr_bits;
	}

	function hashPassword($password)
	{
		$salt = substr(str_replace('-', '', getUUID(41)), 0, 37); // 37 characters for the salt, but remove the 4 dashes, and make sure its 37 characters
		$hash = crypt($password, '$6$rounds=100000$'.$salt.'$');
		if(strlen($hash) <= 13) // crypt will return less than 13 characters if it fails
		{
			return '';
		}
		return $hash;
	}

	function updateSessionData()
	{
		global $dbSession, $SESSION;
		$tempArr = $SESSION;
		unset($tempArr['username']);
		unset($tempArr['ip']);
		unset($tempArr['hash']);
		$serial = pg_escape_string(serialize($tempArr));
		sendQuery($dbSession, "UPDATE sessions SET data='$serial' WHERE hash='{$SESSION['hash']}';");
	}

	function getSessionID()
	{
		global $dbSession;
	// in any normal case, we should never had a collision, but if we do, we should deal with it
		$hash = '';
		$isUnique = False;
		do
		{
			$hash = hash("sha256", str_replace('-', '', getUUID(64)));
			$escapedHash = pg_escape_string($hash);
			$checkQuery = sendQuery($dbSession, "SELECT EXISTS(SELECT 1 FROM sessions WHERE hash='{$escapedHash}' LIMIT 1) AS exists;");
			$exists = pg_fetch_object($checkQuery);
		}while($hash == '' || $exists->exists == 't');
		return $hash;
		
	}
	
?>
