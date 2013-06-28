<?php
	date_default_timezone_set("America/Vancouver"); 
	@include('passwords.priv');
	@include('descParser.inc.php');

	$showEdit = false;

	if(isset($_GET['edit']))
	{
		$showEdit = true;
	}
	
	function getDaySuffix($day)
	{
		$lastDigit = $day % 10;
		switch($lastDigit)
		{
			case 1:return "st"; break;
			case 2:return "nd"; break;
			case 3:return "rd"; break;
			default: return "th"; break;
		}
	}
			
	
	function getMonthName($month)
	{
		while($month > 12)
		{
			$month -= 12;
		}
		while($month < 1)
		{
			$month += 12;
		}		global $sql_host, $sql_database, $sql_user, $sql_password;
		$out = pg_connect("host={$sql_host} dbname={$sql_database} user={$sql_user} password={$sql_password}");
		switch ($month)
		{
			case 1: return "January"; break;
			case 2: return "February"; break;
			case 3: return "March"; break;
			case 4: return "April"; break;
			case 5: return "May"; break;
			case 6: return "June"; break;
			case 7: return "July"; break;
			case 8: return "August"; break;
			case 9: return "September"; break;
			case 10: return "October"; break;
			case 11: return "November"; break;
			case 12: return "December"; break;
			default: return "Unknown"; break;
		}  
	}
	
	$failed = false;
	
	$year = "";
	$month = "";
	$day = "";
	
	if(isset($_GET["year"]))
	{
		$year = $_GET["year"];
	}else
	{
		$failed = true;
	}
	
	if(isset($_GET["month"]))
	{
		$month = $_GET["month"];
	}else
	{
		$failed = true;
	}
	
	if(isset($_GET["day"]))
	{
		$day = $_GET["day"];
	}else
	{
		$failed = true;
	}
	
	if(!$failed && $year != "" && $month != "" && $day != "" && is_numeric($year) && is_numeric($month) && is_numeric($day))
	{
		$dayStart = pg_escape_string(mktime(0,0,0, $month, $day, $year));
		$dayEnd = pg_escape_string(mktime(0,0,0, $month, $day+1, $year));
		// get the info
		echo "<h1>".getMonthName($month)." ".$day.getDaySuffix($day).", ".$year."</h1>";
		$dbconn = pg_connect("host={$sql_host} dbname={$sql_database} user={$sql_user} password={$sql_password}");
		// dstart dend name loc desc author ip
		// get events that either start or end on the day
		//$makeQuery = "CREATE TABLE IF NOT EXISTS events (datestart int8, dateend int8, name varchar(255), location varchar(255), description varchar(65535), author varchar(255), ip varchar(255), PRIMARY KEY(datestart, dateend, name));";
		
		$dbresult = pg_query($dbconn, "SELECT id,sdate,edate,name,loc,descript FROM events WHERE ( (sdate>={$dayStart} AND sdate<{$dayEnd}) OR (edate>{$dayStart} AND edate<{$dayEnd}) OR (sdate<{$dayStart} AND edate>{$dayEnd}) ) ORDER BY sdate;"); 
		if($dbresult == null)
		{
			die("<h2>Error: Database query failed!</h2><p>".pg_last_error()."</p>");
		}
		// start reading data
		if(pg_num_rows($dbresult) == 0)
		{
			echo "<h2>No Events Scheduled</h2>";
		}else
		{
			while($data = pg_fetch_array($dbresult)){
				$eventName = htmlspecialchars($data['name']);
				$eventLocation = htmlspecialchars($data['loc']);
				echo "<h2>$eventName";
				if($showEdit){
					echo "&nbsp;<span class='eventEditLink'><a href='createEvent.php?edit={$data['id']}'><img src='images/edit.png' title='Edit Event'/></a>";
					echo "&nbsp;<a href='createEvent.php?copy={$data['id']}'><img src='images/copy.png' title='Copy Event' /></a></span></h2>";
				}else
				{	
					echo "</h2>";
				}
				if(date("Ymd", $data['sdate']) == date("Ymd", $data['edate']))
				{ // same day
					$startTime = date("g:i a", $data['sdate']);
					$endTime = date("g:i a", $data['edate']);
				}else
				{ // multiple days
					$startTime = date('F jS \a\t g:i a', $data['sdate']);
					$endTime = date('F jS \a\t g:i a', $data['edate']);
				}
				if($startTime == $endTime)
				{
					echo "<small>When: $startTime<br/>Where: $eventLocation</small>";
				}else
				{
					echo "<small>When: $startTime until $endTime<br/>Where: $eventLocation</small>";
				}
				echo '<div class="eventDesc">'.descParser(htmlspecialchars($data['descript'])).'</div>';
				echo '<br/>';
			}
		}
	}else
	{
		echo "<h1>Not a valid date!</h1>";
	}

?>
