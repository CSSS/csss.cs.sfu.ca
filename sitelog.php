<?php 
	@include('includes/sessions.inc.php'); 
	define('TITLE', 'Site Log');

	$logsPerPage = 30;
	
	if($SESSION['isAdmin'] == false){
		// not admin, kick out to account
		Header('Location: https://'.$_SERVER['HTTP_HOST'].'/account.php');
		exit();
	}

	$offset = 0;
	if(isset($_GET['c']))
	{
		$offset = intval($_GET['c']);
		if($offset < 0)
		{
			$offset = 0;
		}
	}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<link rel="stylesheet" href="includes/style.css" type="text/css" />

<?php @include('includes/header.inc.php'); ?>

<div id='content'>
	<div style='float:right;text-align:right;padding:10px;text-decoration:underline;'><a href='admin.php'>Go Back</a></div>
	<h1>Site Log</h1>
	<div id="userTable"><table><tr><th>User Name</th><th>Message</th><th>Time</th><th>IP</th></tr>
		<?php
			$logCountQuery = sendQuery($dbSession, "SELECT COUNT(microtime) AS count FROM sitelog;");
			$data = pg_fetch_object($logCountQuery);
			$totalLogs = $data->count;
			if($totalLogs <= $logsPerPage || $offset >= $totalLogs)
			{
				$offset = 0;
			}
			$logQuery = sendQuery($dbSession, "SELECT username,microtime,ip,message FROM sitelog ORDER BY microtime DESC LIMIT $logsPerPage OFFSET $offset;");
			while($data = pg_fetch_object($logQuery))
			{
				list($tempDate, $notneeded) = explode(' ', $data->microtime);
				$formatDate = date("d/m/Y - h:i:sa", $tempDate);
				$username = htmlspecialchars($data->username);
				$message = htmlspecialchars($data->message);
				$ip = htmlspecialchars($data->ip);
				echo "<tr><td>$username</td><td>$message</td><td>$formatDate</td><td>$ip</td></tr>";
			}
		echo '</table>';
		$lastElement = min($offset + ($totalLogs - $offset), $offset+$logsPerPage);
		$firstElement = $offset+1;
		echo "<div style='text-align:center;width:100%;'>Showing $firstElement to $lastElement of $totalLogs</div>";
		if($offset > 0)
		{
			// show prev button
			$prevCount = ($offset < $logsPerPage ? 0 : $offset-$logsPerPage);
			echo "<div style='float:left;width:50%;text-decoration:underline;'><a href='sitelog.php?c=$prevCount'>&larr; Previous</a></div>";
		}
		if($offset + $logsPerPage < $totalLogs)
		{
			// show next button
			$nextCount = $offset + $logsPerPage;
			echo "<div style='float:right;width:50%;text-align:right;text-decoration:underline;'><a href='sitelog.php?c=$nextCount'>Next &rarr;</a></div>";
		}
		?>
	</div>
</div>
<?php @include('includes/footer.inc.php'); ?>
