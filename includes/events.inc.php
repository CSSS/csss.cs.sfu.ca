<div id="events" class="box">
        <h1><a href='/calendar.php'>Upcoming Events</a></h1>
<?php
include_once("passwords.priv"); // this is a private file excluded from the git which contains pass-phrases and hashes

$dbconn = pg_connect("host=$sql_host dbname=$sql_database user=$sql_user password=$sql_password");

$maxDisplay = 2;
date_default_timezone_set("America/Vancouver");
$dayStart = pg_escape_string(mktime(0,0,0, date('n'), date('j'), date('Y')));
$eventQuery = pg_query($dbconn, "SELECT name, sdate, loc FROM events WHERE edate>$dayStart ORDER BY sdate;");
if($eventQuery == null)
{
	echo "bah";
}else
{
	$arrayCount = pg_num_rows($eventQuery);
	$curEvent = 0;
	while($data = pg_fetch_array($eventQuery))
	{
		if($curEvent == $maxDisplay)
		{
			echo '<div id="hidden_events" style="display: none;">';
		}
		$year = date('Y', $data['sdate']);
		$month = date('n', $data['sdate']);
		$day = date('D', $data['sdate']);
		$name = htmlspecialchars($data['name']);
		$date = date('M jS Y @ g:i a', $data['sdate']);
		$loc = htmlspecialchars($data['loc']);
		echo "<h2><a href='/calendar.php?year=$year&amp;month=$month&amp;day=$day'>$name</a></h2>";
		echo "<small>Date: $date<br />";
		echo "Location: $loc</small>";
		$curEvent++;
	}
	if($arrayCount > $maxDisplay)
	{
		echo "<br /><br />Displaying all upcoming events.<br /></div>";
		echo "<div id='more_events'><br />Displaying $maxDisplay of $arrayCount upcoming events.<br />";
		echo "<a href='#' onclick=\"switchMenu('hidden_events');switchMenu('more_events');return false;\">Show all events</a></div>";
	}
} ?>
      </div>
