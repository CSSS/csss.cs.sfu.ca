<div id="events" class="box">
        <h1>Upcoming Events <!--<a href="http://www.google.com/calendar/feeds/d5v7s5o0u983u14394padcldpjrns02v%40import.calendar.google.com/public/basic" target="_BLANK"><img src="images/xml.gif" alt="XML" style="border: none;" /></a>--></h1>
<?php
// Ensure we've been called internally
defined('CSSS') or die('This is an included page, and cannot be called by it self.');

include("passwords.priv"); // this is a private file excluded from the git which contains pass-phrases and hashes
// it declares $site for the following PHP

function iCalDecoder($site){
		$ical = file_get_contents($site);
		//echo($ical);//
		$ical = str_replace("\r\n ", "", $ical);
		$ical = str_replace("\\n", "<br />", $ical);
		$ical = str_replace("\,", ",", $ical);
		
		preg_match_all('/(BEGIN:VEVENT.*?END:VEVENT)/si', $ical, $result, PREG_PATTERN_ORDER);
		//echo($result[0][0]);//
		for($i = 0; $i < count($result[0]); $i++){
		
			if(substr($result[0][$i], 0, 1) == ' '){
				$result[0][$i] = substr($result[0][$i], 1);
			}
			
			$event = explode("ORGANIZER:", $result[0][$i]);

			$event = explode("DTSTART:", $event[1]);
			
			//Split first
			$event = explode("DTEND:", $event[1]);
			
			//Start is @ $event[0]
			//$icalarray[$i]['DTSTART'] = $event[0];
			$icalarray[$i]['DTSTART'] = substr($result[0][$i], strpos($result[0][$i], 'DTSTART:')+8, strpos($result[0][$i], 'DTEND:') - strpos($result[0][$i], 'DTSTART:')-8);

			$event = explode("UID:", $event[1]);
			//End is @ $event[0]
			//$icalarray[$i]['DTEND'] = $event[0];
			$icalarray[$i]['DTEND'] = substr($result[0][$i], strpos($result[0][$i], 'DTEND:')+6, strpos($result[0][$i], 'UID:') - strpos($result[0][$i], 'DTEND:')-6);

			$event = explode("SUMMARY:", $event[1]);
			//UID is @ $event[0]
			//$icalarray[$i]['UID'] = $event[0];
			$icalarray[$i]['UID'] = substr($result[0][$i], strpos($result[0][$i], 'UID:')+4, strpos($result[0][$i], 'SUMMARY:') - strpos($result[0][$i], 'UID:')-4);

			$event = explode("LOCATION:", $event[1]);
			//Summary is @ $event[0]
			//$icalarray[$i]['SUMMARY'] = $event[0];
			$icalarray[$i]['SUMMARY'] = substr($result[0][$i], strpos($result[0][$i], 'SUMMARY:')+8, strpos($result[0][$i], 'LOCATION:') - strpos($result[0][$i], 'SUMMARY:')-8);
			
			$event = explode("URL:", $event[1]);
			//Location is @ $event[0]
			//$icalarray[$i]['LOCATION'] = $event[0];
			$icalarray[$i]['LOCATION'] = substr($result[0][$i], strpos($result[0][$i], 'LOCATION:')+9, strpos($result[0][$i], 'URL:') - strpos($result[0][$i], 'LOCATION:')-9);
			
			$event = explode("DESCRIPTION:", $event[1]);
			//URL is in $event[0]
			//$icalarray[$i]['URL'] = $event[0];
			$icalarray[$i]['URL'] = substr($result[0][$i], strpos($result[0][$i], 'URL:')+4, strpos($result[0][$i], 'DESCRIPTION:') - strpos($result[0][$i], 'URL:')-4);
			
			$event = explode("CLASS:", $event[1]);
			//Description is @ $event[0]
			//Close up last explosion
			//$icalarray[$i]['DESCRIPTION'] = $event[0];	
			$icalarray[$i]['DESCRIPTION'] = substr($result[0][$i], strpos($result[0][$i], 'DESCRIPTION:')+12, strpos($result[0][$i], 'http://www.facebook.com/event.php') - strpos($result[0][$i], 'DESCRIPTION:')-61);
		}
		
		return $icalarray;
	}
	
$icalarray = iCalDecoder($site);

function formatTime($oldtime){
	// Example time: 20090630T113000
	// Year - Month - Day - Time
	$time['YEAR'] = substr($oldtime, 0, 4);
	$time['MONTH'] = substr($oldtime, 4, 2);
	$time['DAY'] = substr($oldtime, 6, 2);
	$time['TIME'] = substr($oldtime, 9, 4);
	
	$month = array('Jan.', 'Feb.', 'March', 'April', 'May', 'June', 'July', 'Aug.', 'Sept.', 'Oct.', 'Nov.', 'Dec.');
	
	$time['MONTH'] = $month[(int)$time['MONTH']-1];
	// DST fix
	date_default_timezone_set('America/Vancouver');
	//$time['HOUR'] = substr($time['TIME'], 0, 2) - 7; // -7 to change to Pacific Standard Time
	$time['HOUR'] = substr($time['TIME'], 0, 2) + ((int)date("O")/100); // add on the timezone change (-7 for normal, -8 for DST)
		
	$time['MIN'] = substr($time['TIME'], 2, 2);
	
	// Check if timezone change also changes the day
	if($time['HOUR'] < 0){
				// If so, make it the correct day and give back 24 hours
				$time['DAY'] -= 1;
				$time['HOUR'] += 24;
	}
	// Convert to twelve hour time and set TOD to AM or PM
	if($time['HOUR'] > 12 && $time['HOUR'] < 24){
			$time['HOUR'] -= 12;
			$time['TOD'] = 'PM';
			
	}else{
			$time['TOD'] = 'AM';
	}
	return $time;
}

$maxDisplay = 2;
$arrayCount = count($icalarray);
for($i = $arrayCount-1; $i >= 0; $i--)
{
	if($i == $arrayCount - $maxDisplay - 1)
	{
		echo '<div id="hidden_events" style="display: none;">';
	}
	$time = formatTime($icalarray[$i]['DTSTART']);
	echo '<h2><a href="'.$icalarray[$i]['URL'].'">'.$icalarray[$i]['SUMMARY'].'</a></h2>';
	echo '<small>Date: '.$time['MONTH'].' '.$time['DAY'].', '.$time['YEAR'].' at '.$time['HOUR'].':'.$time['MIN'].' '.$time['TOD'].'<br />';
	echo 'Location: '.$icalarray[$i]['LOCATION'].'</small>';
}
if($arrayCount > $maxDisplay)
{
?>

	<br /><br />
	Displaying all upcoming events.<br />
	</div>
	<div id="more_events">
        <br />
	Displaying <?= $maxDisplay ?> of <?= $arrayCount ?> upcoming events.<br />
	<a href="#" onclick="switchMenu('hidden_events');switchMenu('more_events');return false;">Show all events</a>
	</div>
<?php
}
?>
<hr style="margin-top: 14px; margin-bottom: 14px;" />
	<p>Follow us on <a href="http://www.facebook.com/group.php?gid=2203105681" target="_blank">Facebook</a> for the latest events.</p>
      </div>
