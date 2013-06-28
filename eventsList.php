<?php
	@include('includes/sessions.inc.php');
	define('TITLE', 'All Events');

	$eventsPerPage = 30;

	date_default_timezone_set("America/Vancouver");

	$now = time();

	$offset = $now;
	$offsetForward = true;

	if(isset($_GET['ndate']))
	{
		$offset = intval($_GET['ndate']);
		$offsetForward = true;
	}elseif(isset($_GET['b']))
	{
		$offset = intval($_GET['ldate']);
		$offsetForward = false;
	}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<link rel="styleshee" href="includes/style.css" type="text/css" />

<?php @include('includes/header.inc.php'); ?>

<div id='content'>
	<div style='float:right;text-align:right;padding:10px;'><u><a href='account.php'>Go Back</a></u></div>
	<h1>Events</h1>
	<div id="userTable"><table><tr><th>Event Name</th><th>Event Date/Time</th><th>Edit Event?</th></tr>
		<?php
			if($offsetForward)
			{
				$eventQuery2 = NULL;
				$eventQuery = sendQuery($dbSession, "SELECT name, sdate, edate, id FROM events WHERE sdate>=$offset ORDER BY sdate LIMIT $eventsPerPage");
				$queryCount = pg_num_rows($eventQuery);
				if($queryCount < $eventsPerPage)
				{
					$eventQuery2 = sendQuery($dbSession, "SELECT name, sdate, edate, id FROM events WHERE sdate<$offset ORDER BY sdate DESC LIMIT $eventsPerPage-$queryCount;");
				}
			}else
			{
				$eventQUery = sendQuery($dbSession, "SELECT name, sdate, edate, id FROM events WHERE sdate<$offset ORDER BY sdate DESC LIMIT $eventsPerPage;");	
			}
			while($data = pg_fetch_array($eventQuery);
