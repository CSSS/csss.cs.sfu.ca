<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
  
	<link rel="stylesheet" href="includes/style.css" type="text/css" />
	<link rel="stylesheet" href="includes/calendar.css" type="text/css" />
	
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8/jquery.min.js" type='text/javascript'></script>  
	<script src="includes/jquery.curvycorners.min.js" type='text/javascript'></script>
	<?php 	
	date_default_timezone_set("America/Vancouver"); 
	@include('includes/passwords.priv');

	$dbconn = pg_connect("host={$sql_host} dbname={$sql_database} user={$sql_user} password={$sql_password}");

	$showEdit = false;

	if(isset($_COOKIE['session']))
	{
		$showEdit = true;
	}

	function getURLQuery($year, $month)
	{
		while($month > 12)
		{
			$month -= 12;
			$year += 1;  
		}
		while($month < 1)
		{
			$month += 12;
			$year -= 1;
		}
		if($year <= 1970 || $year >= 2036 )
		{
			$year = $curYear;
		}
		return 'calendar.php?year='.$year.'&amp;month='.$month;
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
		}
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
	
	function getEvent($year, $month, $day)
	{
		return "";
	}
  
	function getWeekDay($year, $month, $day)
	{
		return date('w', strtotime($year . "/" . $month . "/" . $day));
	}
 
	$curYear = date('Y');
	$usedYear = "";
	if(isset($_GET["year"]))
	{
		$usedYear = $_GET["year"];
	}else
	{
		$usedYear = $curYear;
	}
  
	$curMonth = date('n');
	$usedMonth = "";
	if(isset($_GET["month"]))
	{
		$usedMonth = $_GET["month"];
		if($usedMonth == "")
		{
			$usedMonth = $curMonth;
		}
	}else
	{
		$usedMonth = $curMonth;
	}
	while($usedMonth < 1){
		$usedMonth += 12;
		$usedYear -= 1;
	}
	while($usedMonth > 12){
		$usedMonth -= 12;
		$usedYear += 1;
	}

	$curDay = date('j');
  
	if($usedYear <= 1970 || $usedYear >= 2036 )
	{
		$usedYear = $curYear; 
		$usedMonth = $curMonth;
	}

	$usedMonthS = getMonthName($usedMonth);
	$prevMonthS = getMonthName($usedMonth-1);
	$nextMonthS = getMonthName($usedMonth+1);
  
	$dayCount = date('t', strtotime($usedYear."/".$usedMonth."/".$curDay));
	$dayIndex = 1;
	$calendarCount = 0;
  
	$startDay = getWeekDay($usedYear, $usedMonth, 1);
	
	define('TITLE', $usedMonthS." ".$usedYear." Events");?>
		
	<script type="text/javascript">
	<!--
	var maxLoad = 10000;
	
	var currentGETs = new Array();
	var currentINTs = new Array();
	var day = 0;
	var doneFade = false;
	var doneLoad = false;
	var publicData = "";
	var defaultHTML = "<div class='dayClose'><img src='images/close.png' title='Close' alt='Close'/></div><div class='dayCreate'><img src='images/create.png' title='Create New Event' alt='Create New Event'/></div>";
	var defaultHTMLNoCreate = "<div class='dayClose'><img src='images/close.png' title='Close' alt='Close'/></div>";

	var get = location.search.replace('?', '').split('&').map(function(val){
			return val.split('=');
	});
	
    $(document).ready(function(){
		$.ajaxSetup({ timeout: maxLoad });
	
		jQuery.fn.center = function () {
			this.css({"position" : "absolute" , "top" : Math.min(120, (($(window).height() - this.outerHeight()) / 2) + $(window).scrollTop()) + "px" ,
							"left" : Math.max(0, (($(window).width() - this.outerWidth()) / 2) + $(window).scrollLeft()) + "px"});
			return this;

		};
		
		$.fn.showHtml = function(html, speed, callback){
			return this.each(function()
			{
			 // The element to be modified
				var el = $(this);

				// Preserve the original values of width and height - they'll need 
				// to be modified during the animation, but can be restored once
				// the animation has completed.
				var finish = {width: this.style.width, height: this.style.height};

				// The original width and height represented as pixel values.
				// These will only be the same as `finish` if this element had its
				// dimensions specified explicitly and in pixels. Of course, if that 
				// was done then this entire routine is pointless, as the dimensions 
				// won't change when the content is changed.
				var cur = {width: el.width()+'px', height: el.height()+'px'};

				// Modify the element's contents. Element will resize.
				el.html(html);

				// Capture the final dimensions of the element 
				// (with initial style settings still in effect)
				var next = {width: el.width()+'px', height: el.height()+'px'};

				el .css(cur) // restore initial dimensions
				.animate(next, speed, function()  // animate to final dimensions
				{
					el.css(finish); // restore initial style settings
					if ( $.isFunction(callback) ) callback();
				});
			});
		};
		
		$('.cell,.cellh').click(function() {
			day = $(this).attr("id").substr(3);
			$('#dayInfoBack').height($(document).height());
			$('#dayInfoBack').width($(document).width());
			$('#dayInfoBack').fadeIn(300);
			//$('#dayInfo').center();
			$('#dayInfo').fadeIn(300, afterFade);
			doneFade = false;
			doneLoad = false;
			var needLoad = true;
			for(var i = 0; i < currentGETs.length; i++){
				if(currentGETs[i] == day){
					needLoad = false;
				}
			}
			if(needLoad == true){
				var localDay = day;	
				currentGETs.push(day);	
				
				currentINTs.push(setTimeout(function() {
					for(var i = 0; i < currentGETs.length; i++){
						if(currentGETs[i] == localDay){
							currentGETs[i] = currentGETs[currentGETs.length-1];
							currentGETs.pop();
							break;
						}
					}
					if(localDay == day){
						doneLoad = true;
						if(doneFade == true){
							$('#dayInfo').showHtml(defaultHTMLNoCreate+"<h1>Error</h1><h2>Failed to load Data</h2>"+
								"<p>Please try again later.  If the problem persists, please contact the webmaster at <a href='mailto:csss-exec@sfu.ca'>csss-exec@sfu.ca</a></p>", 500);
							//$('#dayInfo').css({"background" : "#FFF" , "min-height" : "10px" , "padding" : "10px"});
							$('.dayClose').click(closeInfo);
						}else
						{
							publicData = data;
						}
					} 
				}, maxLoad+100)); 	
<?php if($showEdit){ ?>
				$.get("includes/dayDetails.php?year=<?php echo $usedYear ?>&month=<?php echo $usedMonth ?>&day="+day+"&edit", function(data){
<?php }else{ ?>
				$.get("includes/dayDetails.php?year=<?php echo $usedYear ?>&month=<?php echo $usedMonth ?>&day="+day, function(data){
<?php } ?>
					for(var i = 0; i < currentGETs.length; i++){
						if(currentGETs[i] == localDay){
							currentGETs[i] = currentGETs[currentGETs.length-1];
							clearInterval(currentINTs[i]);
							currentINTs[i] = currentINTs[currentINTs.length-1];
							currentGETs.pop();
							currentINTs.pop();
							break;
						}
					}
					if(localDay == day){
						doneLoad = true;
						if(doneFade == true){
<?php if($showEdit){ ?>
							$('#dayInfo').showHtml(defaultHTML+data, 300);
<?php }else{ ?>
							$('#dayInfo').showHtml(defaultHTMLNoCreate+data, 300);
<?php } ?>
							//$('#dayInfo').css({"background" : "#FFF" , "min-height" : "10px" , "padding" : "10px"});
							$('.dayClose').click(closeInfo);
							$('.dayCreate').click(createEvent);
						}else
						{
							publicData = data;
						}
					}
				});
			}
		});
		
		function afterFade(){
			doneFade = true;
			if(doneLoad == true){
					//$('#dayInfo').html("</div><div class='dayClose'><img src='images/close.gif'></div>"+publicData, 300);
<?php if($showEdit){ ?>
				$('#dayInfo').showHtml(defaultHTML+publicData, 300);
<?php }else{ ?>
				$('#dayInfo').showHtml(defaultHTMLNoCreate+publicData, 300);
<?php } ?>
				$('.dayClose').click(closeInfo);
				$('.dayCreate').click(createEvent);
				publicData = "";
			}
		}

		function closeInfo(){
			day = 0;
			$('#dayInfoBack').fadeOut(150, function() {
				$('#dayInfoBack').height("100%");
				$('#dayInfoBack').width("100%");
			});
			$('#dayInfo').fadeOut(150, function() {
				$('#dayInfo').html("<div class='dayLoading'><img src='images/loading.gif'></div>"+defaultHTMLNoCreate);
				$('.dayClose').click(closeInfo);
				//$('#dayInfo').css({"top" : "0px", "left" : "0px"});
			});
		}

		function createEvent(){
			var curDate = new Date(<?php echo $usedYear; ?>, <?php echo $usedMonth ?>-1, day);
			window.location.href = 'https://'+window.location.hostname+'/createEvent.php?sdate='+(curDate.getTime() / 1000);
		}

		$('#dayInfoBack').click(closeInfo);	
		
		$('.dayClose').click(closeInfo);

		for(var i = 0; i < get.length; i++)
		{
			if(get[i][0] == "day")
			{
				$('div [data-id="'+get[i][1]+'"]').click();
				break;
			}
		}

    });
	//-->
   </script>
	
	<?php	
	@include('includes/header.inc.php'); ?>
	
	<div id="dayInfoBack"></div>
	<div id="dayInfo"><div class="dayLoading"><img src="images/loading.gif" alt='Loading'/></div><div class="dayClose"><img src="images/close.png" alt='Close'/></div></div>
  
	<div id="calendar">
	
	<div id="calendar-header">
		<?php
			echo '<div id="calendar-header-left"><a href="'.getURLQuery($usedYear, $usedMonth-1).'">&larr; '.$prevMonthS.'</a></div>';
			echo '<div id="calendar-header-mid"><h1>'.$usedMonthS.' '.$usedYear.'</h1></div>';
			echo '<div id="calendar-header-right"><a href="'.getURLQuery($usedYear, $usedMonth+1).'">'.$nextMonthS.' &rarr;</a></div>';
		?>
	</div>

	<div class="day">Sunday</div>
	<div class="day">Monday</div>
    <div class="day">Tuesday</div>
    <div class="day">Wednesday</div>
    <div class="day">Thursday</div>
    <div class="day">Friday</div>
    <div class="day">Saturday</div>
    <?php 
	while($startDay > 0)
	{ 
		echo '<div class="cellg"></div>';
		$startDay--;
		$calendarCount++;
	}
	while($dayIndex <= $dayCount)
	{
		// get events for day;
		if ( $dayIndex == $curDay && $curMonth == $usedMonth && $curYear == $usedYear )
		{
			echo '<div class="cellh" id="day'.$dayIndex.'">';
		}else
		{
			echo '<div class="cell" id="day'.$dayIndex.'">';
		}
		echo '<noscript><div><a href="dayInfo.php?year='.$usedYear.'&amp;month='.$usedMonth.'&amp;day='.$dayIndex.'"><span class="clickable"></span></a></div></noscript>';
		echo '<h2>'.$dayIndex . '</h2>';
		// get any events from db
		$dayStart = pg_escape_string(mktime(0,0,0, $usedMonth, $dayIndex, $usedYear));
		$dayEnd = pg_escape_string(mktime(0,0,0, $usedMonth, $dayIndex+1, $usedYear));
		// get events where event starts during the day, or the event ends during the day, or the event spans over the entire day
		$eventsQuery = pg_query($dbconn, "SELECT name FROM events WHERE ( (sdate>=$dayStart AND sdate<$dayEnd) OR (edate>$dayStart AND edate<$dayEnd) OR (sdate<$dayStart AND edate>$dayEnd)) ORDER BY sdate;");
		$eventCount = 0;
		$eventTotal = pg_num_rows($eventsQuery);
		while($data = pg_fetch_array($eventsQuery))
		{
			if($eventCount >= 4)
			{
				echo '<div class="cellMore"> and '.($eventTotal-$eventCount).' more... </div>';
				break;
			}
			echo '<div class="cellName">'.htmlspecialchars($data['name']).'</div>';
			$eventCount += 1;
		}

		echo '</div>';
		$dayIndex++;
		$calendarCount++;
	}
	while($calendarCount % 7 != 0){
		echo '<div class="cellg"></div>';
		$calendarCount++;
	}?> 
	<div id="calendar-bot"></div>
	
    
</div>

<?php @include('includes/footer.inc.php'); ?>
