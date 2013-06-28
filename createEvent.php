<?php 
	@include('includes/sessions.inc.php');
	@include('includes/descParser.inc.php');

	class EventType
	{
		const typeNew = 0;
		const typeEdit = 1;
		const typeCopy = 2;
	}

	class EventMode
	{
		const modeNew = 0;
		const modePreview = 1;
		const modeSubmit = 2;
		const modeDelete = 3;
	}

	class EventState
	{
		const stateOK = 0;
		const stateBad = 1;
	}

	$eventMessage = '';
	$editMessage = '';

	$eventType = EventType::typeNew;
	$eventMode = EventMode::modeNew;
	$eventState = EventState::stateOK;
	
	$eventData = array();
	
	if(isset($_GET['edit']))
	{
		$eventType = EventType::typeEdit;
		if(empty($_GET['edit']))
		{
			$editMessage = 'Edit Error: No event specified to edit!';
			$eventType = EventType::typeNew;
			$eventState = EventState::stateBad;
		}else{
			$eventData['edit'] = pg_escape_string($_GET['edit']);
			// get the rest of the data from the database
			$getQuery = SendQuery($dbSession, "SELECT * FROM events WHERE id={$eventData['edit']};");
			if(pg_num_rows($getQuery) == 0){
				// no event
				$eventType = EventType::typeNew;
				$eventState = EventState::stateBad;
				$editMessage = 'Edit Error: Event does not exists to edit!';
			}else{
				$eventState = EventState::stateOK;
				$eventData = array_merge($eventData, pg_fetch_array($getQuery)); // merge our data together
				// we have the data
				// we need to format the dates and times however
				$sdate = $eventData['sdate'];
				$edate = $eventData['edate'];
				$eventData['sdate'] = date("m/d/Y", $sdate);
				$eventData['stime'] = date("g:i a", $sdate);
				$eventData['edate'] = date("m/d/Y", $edate);
				$eventData['etime'] = date("g:i a", $edate);
			}
		}
	}elseif(isset($_POST['delete']))
	{
		$eventMode = EventMode::modeDelete;
		$eventData = $_POST;
		
		$id = pg_escape_string($eventData['edit']);
		$name = pg_escape_string($eventData['name']);
		
		$checkQuery = SendQuery($dbSession, "SELECT sdate FROM events WHERE id='$id';");

		if(pg_num_rows($checkQuery) != 0)
		{
			$data = pg_fetch_array($checkQuery);
			$deleteQuery = SendQuery($dbSession, "DELETE FROM events WHERE id='$id';");
			$eventMessage = 'Event deleted successfully!';
			$eventState = EventState::stateOK;
			writeToLog($dbSession, $SESSION['username'], $SESSION['ip'], "Deleted event $id which was named '$name'.");
			// redirect to calendar
			$year = date("Y", $data['sdate']);
			$month = date("m", $data['sdate']);
			header('Location: //'.$_SERVER['HTTP_HOST']."/calendar.php?year=$year&month=$month");
			die();
		}else
		{
			$eventMessage = 'Could not delete event!  It does not exist!';
			$deleteFailed = EventState::stateBad;
			writeToLog($dbSession, $SESSION['username'], $SESSION['ip'], "Tried to delete event $id which didn't exist!");
		}
	}elseif(isset($_POST['submit']) || isset($_POST['preview']))
	{
		if(isset($_POST['submit']))
		{	
			$eventMode = EventMode::modeSubmit;
		}else
		{
			$eventMode = EventMode::modePreview;
		}
		$eventData = $_POST;
		// we have a query sent
		if(empty($_POST['name']))
		{
			$eventMessage = 'No name was given!';
			$eventState = EventState::stateBad;
		}
		elseif(empty($_POST['loc']))
		{
			$eventMessage = 'No location was given!';
			$eventState = EventState::stateBad;
		}
		elseif(empty($_POST['sdate']))
		{
			$eventMessage = 'No start date was given!';
			$eventState = EventState::stateBad;
		}
		elseif(empty($_POST['edate']))
		{
			$eventMessage = 'No end date was given!';
			$eventState = EventState::stateBad;
		}
		elseif(empty($_POST['stime']))
		{
			$eventMessage = 'No start time was given!';
			$eventState = EventState::stateBad;
		}
		elseif(empty($_POST['etime']))
		{
			$eventMessage = 'No end time was given!';
			$eventState = EventState::stateBad;
		}elseif(empty($_POST['descript']))
		{
			$eventMessage = 'No description was given!';
			$eventState = EventState::stateBad;
		}elseif(!preg_match('/^([0-9]{2})\/([0-9]{2})\/([0-9]{4})/', $_POST['sdate'], $sdateMatches))
		{
			$eventMessage = 'Start date is not formatted correctly! Expected mm/dd/yyyy';
			$eventState = EventState::stateBad;
		}elseif(!preg_match('/^([0-9][0-9]?):([0-9]{2}) (am|pm)/', $_POST['stime'], $stimeMatches))
		{
			$eventMessage = 'Start time is not formatted correctly! Expected h:mm am/pm';
			$eventState = EventState::stateBad;
		}elseif(!preg_match('/^([0-9]{2})\/([0-9]{2})\/([0-9]{4})/', $_POST['edate'], $edateMatches))
		{
			$eventMessage = 'End date is not formatted correctly! Expected mm/dd/yyyy';
			$eventState = EventState::stateBad;
		}elseif(!preg_match('/^([0-9][0-9]?):([0-9]{2}) (am|pm)/', $_POST['etime'], $etimeMatches))
		{
			$eventMessage = 'End time is not formatted correctly! Expected h:mm am/pm';
			$eventState = EventState::stateBad;
		}else
		{
			if($stimeMatches[3] == 'am')
			{
				if($stimeMatches[1] == '12')
				{
					$sHour = 0;
				}else
				{
					$sHour = $stimeMatches[1];
				}
			}else
			{
				if($stimeMatches[1] == '12')
				{
					$sHour = 12;
				}else
				{
					$sHour = $stimeMatches[1] + 12;
				}
			}
			if($etimeMatches[3] == 'am')
			{
				if($etimeMatches[1] == '12')
				{
					$eHour = 0;
				}else
				{
					$eHour = $etimeMatches[1];
				}
			}else
			{
				if($etimeMatches[1] == '12')
				{
					$eHour = 12;
				}else
				{
					$eHour = $etimeMatches[1] + 12;
				}
			}

			$sdatetime = mktime($sHour, $stimeMatches[2], 0, $sdateMatches[1], $sdateMatches[2], $sdateMatches[3]);
			$edatetime = mktime($eHour, $etimeMatches[2], 0, $edateMatches[1], $edateMatches[2], $edateMatches[3]);
			if(!$sdatetime)
			{
				$eventMessage = 'Start date and time are not valid!';
				$eventState = EventState::stateBad;
			}elseif(!$edatetime)
			{
				$eventMessage = 'End date and time are not valid!';
				$eventState = EventState::stateBad;
			}elseif($sdatetime > $edatetime)
			{
				$eventMessage = 'Event ends before it starts!';
				$eventState = EventState::stateBad;
			}else
			{
				$sdatetimeV = date('m/d/Y g:i a', $sdatetime);
				$edatetimeV = date('m/d/Y g:i a', $edatetime);
				if($sdatetimeV != $sdateMatches[0].' '.$stimeMatches[0])
				{
					$eventMessage = 'Start date and time are not formatted properly!';
					$eventState = EventState::stateBad;
				}elseif($edatetimeV != $edateMatches[0].' '.$etimeMatches[0])
				{
					$eventMessage = 'End date and time are not formatted properly!';
					$eventState = EventState::stateBad;
				}elseif($eventMode == EventMode::modePreview)
				{
					$eventMessage = 'No problems found.';
					$eventState = EventState::stateOK;
				}else
				{
					// escape inputs
					$name = pg_escape_string($eventData['name']);
					$loc = pg_escape_string($eventData['loc']);
					$descript = pg_escape_string($eventData['descript']);
					
					// are we editing?
					if(isset($_POST['edit']))
					{
						// editing, escape more inputs
						$id = pg_escape_string($eventData['edit']);

						$existsQuery = sendQuery($dbSession, "SELECT 1 FROM events WHERE id=$id LIMIT 1;");
						if(pg_num_rows($existsQuery) > 0)
						{
							$updateQuery = sendQuery($dbSession, "UPDATE events SET sdate=$sdatetime, edate=$edatetime, name='$name', loc='$loc', descript='$descript', author='{$SESSION['username']}', ip='{$SESSION['ip']}' WHERE id=$id;");
							// success!
							$eventState = EventState::stateOK;
							writeToLog($dbSession, $SESSION['username'], $SESSION['ip'], "Edited event $id which is named '$name'.");
						}else
						{
							// not able to edit
							$eventMessage = 'Unable to edit the event!  The original event does not exist anymore!';
							$eventState = EventState::stateBad;
						}
					}else
					{
						// new event
						$updateQuery = sendQuery($dbSession, "INSERT INTO events (sdate, edate, name, loc, descript, author, ip) VALUES ($sdatetime, $edatetime, '$name', '$loc', '$descript', '{$SESSION['username']}', '{$SESSION['ip']}') RETURNING id;");
						$data = pg_fetch_array($updateQuery);
						// success!
						writeToLog($dbSession, $SESSION['username'], $SESSION['ip'], "Created the new event {$data['id']} which is named '$name'.");
						$eventState = EventState::stateOK;
						// redirect
						
					}
					if($eventState == EventState::stateOK)
					{
						$year = date("Y", $sdatetime);
						$month = date("m", $sdatetime);
						$day = date("d", $sdatetime);
						header('Location: //'.$_SERVER['HTTP_HOST']."/calendar.php?year=$year&month=$month&day=$day");
						die();
					}
				}
			}
		}
	}elseif(isset($_GET['sdate']))
	{
		if(!empty($_GET['sdate']))
		{
			$eventData['sdate'] = date("m/d/Y", $_GET['sdate']);
			$eventData['edate'] = date("m/d/Y", $_GET['sdate']);
		}
	}elseif(isset($_GET['copy']))
	{
		if(!empty($_GET['copy']))
		{
			$id = pg_escape_string($_GET['copy']);
			$copyQuery = sendQuery($dbSession, "SELECT name, loc, sdate, edate, descript FROM events WHERE id=$id");
			if(pg_num_rows($copyQuery) != 0)
			{
				$eventData = pg_fetch_array($copyQuery);
				$eventData['stime'] = date("g:i a", $eventData['sdate']);
				$eventData['sdate'] = date("m/d/Y", $eventData['sdate']);
				$eventData['etime'] = date("g:i a", $eventData['edate']);
				$eventData['edate'] = date("m/d/Y", $eventData['edate']);
				$eventType = EventType::typeCopy;
				$editMessage = 'Copying event '.htmlspecialchars($eventData['name']).' from '.$eventData['sdate'].'.';
			}

		}
	}
	// if a new edit that isn't bad, or an old edit that isn't deleted
	if( ($eventType == EventType::typeEdit && $eventMode == EventMode::modeNew && $eventState != EventState::stateBad ) || ( isset($_POST['edit']) && ( ($eventMode == EventMode::modeDelete && $eventState == EventState::stateBad) || $eventMode != EventMode::modeDelete ) ) )
	{
		$eventType = EventType::typeEdit;
		$editMessage = 'Editing "'.htmlspecialchars($eventData['name']).'" on '.$eventData['sdate'];
		define('TITLE', 'Edit Event');
	}else
	{
		define('TITLE', 'Create Event');
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns='http://www.w3.org/1999/xhtml'>
	<head>
	<link rel="stylesheet" href="//ajax.aspnetcdn.com/ajax/jquery.ui/1.8.18/themes/smoothness/jquery-ui.css" />
	<link rel='stylesheet' href='includes/jquery.timePicker.css' />
	<link rel='stylesheet' href='includes/style.css' type='text/css' />
	<link rel='stylesheet' href='includes/calendar.css' type='text/css' />
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js" type='text/javascript'></script>
	<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.9.0/jquery-ui.min.js" type='text/javascript'></script>
	<script type='text/javascript'> <!--
		$(function() {
			function getAutoComplete(entered)
			{
				var results = [];
				var hourParts = entered.match(/^(1[0-2]?|[2-9]):?/);
				var minuteParts = entered.match(/^(1[0-2]?|[2-9]):([0-5][0-9]?) ?/);
				var fullParts = entered.match(/^(1[0-2]?|[2-9]):([0-5][0-9]) (a.?|p.?)/);
				console.debug(hourParts);
				console.debug(minuteParts);
				console.debug(fullParts);
				if(fullParts && fullParts.length == 4)
				{
					if(fullParts[3].substring(0,1) == 'a')
					{
						results.push(fullParts[1]+':'+fullParts[2]+' am');
						results.push(fullParts[1]+':'+fullParts[2]+' pm');
					}else
					{
						results.push(fullParts[1]+':'+fullParts[2]+' pm');
						results.push(fullParts[1]+':'+fullParts[2]+' am');
					}
				}else if(minuteParts && minuteParts.length == 3)
				{
					if(minuteParts[2].length == 1)
					{
						results.push(minuteParts[1]+':'+minuteParts[2]+'0 am');
						results.push(minuteParts[1]+':'+minuteParts[2]+'5 am');
						results.push(minuteParts[1]+':'+minuteParts[2]+'0 pm');
						results.push(minuteParts[1]+':'+minuteParts[2]+'5 pm');
					}else
					{
						results.push(minuteParts[1]+':'+minuteParts[2]+' am');
						results.push(minuteParts[1]+':'+minuteParts[2]+' pm');
					}
				}else if(hourParts)
				{
					results.push(hourParts[1]+':00 am');
					results.push(hourParts[1]+':15 am');
					results.push(hourParts[1]+':30 am');
					results.push(hourParts[1]+':45 am');
					results.push(hourParts[1]+':00 pm');
					results.push(hourParts[1]+':15 pm');
					results.push(hourParts[1]+':30 pm');
					results.push(hourParts[1]+':45 pm');
				}else
				{
					results.push('12:00 am');
					// some common times
					for(var i = 1; i < 12; i++)
					{
						results.push(i+':00 am');
					}
					results.push('12:00 pm');
					for(var i = 1; i < 12; i++)
					{
						results.push(i+':00 pm');
					}
				}
				return results;
			}

			function removeInvalidTimes(inputArr, stime)
			{
				results = jQuery.grep(inputArr, function(a)
				{
					var sparts = stime.match(/^([0-9][0-9]?):([0-9]{2}) (am|pm)/);
					var eparts = a.match(/^([0-9][0-9]?):([0-9]{2}) (am|pm)/);
					if(!sparts || sparts.length != 4)
					{
						return true;
					}
					var shour = (sparts[1] == '12' ? 0 : Number(sparts[1]));
					var s24hour = (sparts[3] == 'am' ? shour : shour + 12);
					var smin = Number(sparts[2]);
					var ehour = (eparts[1] == '12' ? 0 : Number(eparts[1]));
					var e24hour = (eparts[3] == 'am' ? ehour : ehour + 12);
					var emin = Number(eparts[2]);
					if(s24hour*100+smin > e24hour*100+emin )
					{ 
						return false;
					}else
					{
						return true;
					}
				});
				return results;
			}

			function stimeSource(request, response)
			{
				if(request.term == ""){
					results = [];
					results.push("2:00 pm");
					results.push("3:00 pm");
					results.push("4:00 pm");
					results.push("5:00 pm");
					results.push("6:00 pm");
					results.push("7:00 pm");
				}else
				{
					results = getAutoComplete(request.term);
				}
				response(results.slice(0, 6));
			}

			function etimeSource(request, response)
			{
				results = getAutoComplete(request.term);
				if($('#sdate').val() == $('#edate').val() && $('#sdate').val() != '' && $('#stime').val() != '')
				{
					results = removeInvalidTimes(results, $('#stime').val());
				}
				response(results.slice(0, 6));
			}

			$( "#sdate" ).datepicker(
			{
				onClose: function(dateText, inst)
				{
					$("#edate").datepicker('option', 'minDate', dateText);
					if($("#edate").val() == '')
					{
						$("#edate").val(dateText);
					}
				}	
			});

			$( '#edate' ).datepicker();
			<?php if(!empty($eventData['sdate'])){ echo "$( '#edate' ).datepicker('option', 'minDate', '{$eventData['sdate']}');"; } ?>

			$( '#stime' ).autocomplete({ delay:0, minLength:0, autoFocus:true, source: stimeSource });
			$( '#etime' ).autocomplete({ delay:0, minLength:0, autoFocus:true, source: etimeSource });

			$( '#stime' ).focus(function(){
					$(this).autocomplete("search", "");
			});
			$( '#etime' ).focus(function(){
					$(this).autocomplete("search", "");
			});

			$('input').keypress(function(event){return event.keyCode != 13;});

			$(':submit').click(function(e){
				var name = $(this).attr('name');
				if(name == 'delete')
				{
					var ok = confirm("Do you really want to delete this event?");
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

			$('#markdownLink').click(function(e){
				e.preventDefault();
				if($('#markdownEx').is(':hidden'))
				{
					$('#markdownEx').slideDown('normal');
				}else
				{
					$('#markdownEx').slideUp('fast');
				}
			});
		}); -->
	</script>

<?php @include('includes/header.inc.php'); ?>
<div id='content'>
	<?php if($eventType == EventType::typeEdit)
	{
		echo "<h1>Edit Event</h1>";
		echo "<noscript><p><b>You have scripting disabled. It is highly recommended to enable scripting while creating events.</b></p></noscript>";
		if($eventState == EventState::stateOK || $eventMode != EventMode::modeNew )
		{
			echo "<div class='formSuccess'>$editMessage</div>";
		}else
		{
			echo "<div class='formError'>$editMessage</div>";
		}
		echo "<br clear=both /><br />";
	}else
	{
		echo "<h1>Create Event</h1>";
		if($eventType == EventType::typeNew && $eventMode == EventMode::modeNew && $eventState == EventState::stateBad)
		{		
			echo "<div class='formError'>$editMessage</div>";
			echo "<br clear=both /><br />";
		}elseif($eventType == EventType::typeCopy)
		{
			echo "<div class='formSuccess'>$editMessage</div><br clear=both /><br />";
		}
	}?>
	<div id='eventForm'>
		<form action='createEvent.php' method='post'><div>
			<?php if($eventType == EventType::typeEdit)
			{ 
				echo "<input type='hidden' name='edit' value='{$eventData['edit']}' />";
			} ?>
			<label for='name'>Name:</label><input type='text' name='name' placeholder='Event Name' value='<?php if(!empty($eventData['name'])){echo htmlspecialchars($eventData['name']);} ?>'/>
			<label for='loc'>Location:</label><input type='text' name='loc' placeholder='Event Location' value='<?php if(!empty($eventData['loc'])){echo htmlspecialchars($eventData['loc']);} ?>'/><br clear='both'/>
			<label for='sdate'>Start Date:</label><input type='text' name='sdate' id='sdate' placeholder='mm/dd/yyyy' value='<?php if(!empty($eventData['sdate'])){echo htmlspecialchars($eventData['sdate']);} ?>'/>
			<label for='edate'>End Date:</label><input type='text' name='edate' id='edate' placeholder='mm/dd/yyyy' value='<?php if(!empty($eventData['edate'])){echo htmlspecialchars($eventData['edate']);} ?>'/><br clear='both'/>
			<label for='stime'>Start Time:</label><input type='text' name='stime' id='stime' placeholder='hh:mm am/pm' value='<?php if(!empty($eventData['stime'])){echo htmlspecialchars($eventData['stime']);} ?>'/>
			<label for='etime'>End Time:</label><input type='text' name='etime' id='etime' placeholder='hh:mm am/pm' value='<?php if(!empty($eventData['etime'])){echo htmlspecialchars($eventData['etime']);} ?>'/>
			<span style='float:right;text-align:right;margin-right:20px;'><a href='markdown.php' target='_blank' id='markdownLink'>Markdown Help</a></span><br clear='both'/>
			<label for='desc'>Description:</label>
			<textarea name='descript' rows='8' /><?php if(!empty($eventData['descript'])){echo htmlspecialchars($eventData['descript']);} ?></textarea><br clear='both'/>
			<span style='float:left;margin-left:103px;'>
			<?php if($eventType == EventType::typeEdit){
				echo "<input name='submit' type='submit' value='Update Event' />";
			}else{
				echo "<input name='submit' type='submit' value='Create Event' />";
			}?>
			<input name='preview' type='submit' value='Preview' />
			<?php if($eventType == EventType::typeEdit){ 
				echo "<input name='delete' type='submit' value='Delete Event' />";
			}?>			
			</span>
			<br clear=both />
			<?php 
				if($eventMode == EventMode::modePreview || $eventMode == EventMode::modeSubmit || $eventMode == EventMode::modeDelete)
				{
					if($eventState == EventState::stateBad)
					{ 
						echo "<div class='formError'>$eventMessage</div>";
					}else
					{
						echo "<div class='formSuccess'>$eventMessage</div>";
					}
				}?>
		</div></form>
	</div><br clear='both' />
	<div id='markdownEx'>
		<table>
			<tr><th>Markup Code</th><th>Output</th></tr>
			<tr><td>This text is **bold,** //italic,// and __underlined.__</td><td>This text is <b>bold,</b> <i>italic,</i> and <u>underlined.</u></td></tr>
			<tr class='alt'><td>* This is an unordered list<br/>* With multiple entries<br/>&nbsp;* And a sublist.</td>
				<td><div class='eventDesc' style='padding-left:0px;'><ul><li>This is an unordered list</li><li>With multiple entries</li><ul><li>And a sublist.</li></ul></ul></div></td></tr>
			<tr><td># This is an ordered list<br/># With multiple entries<br/>&nbsp;@ And a nested list<br/>&nbsp;@ Using letters</td>
				<td><div class='eventDesc' style='padding-left:0px;'><ol><li>This is an ordered list</li><li>With multiple entries</li><ol type='a'><li>And a nested list</li><li>Using letters</li></ol></ol></div></td></tr>
			<tr class='alt'><td>We can also (Link to Google)[http://www.google.ca].</td>
				<td>We can also <a href='http://www.google.ca'>Link to Google</a>.</td></tr>
			<tr><td colspan='2'>Note: <span style='font-family:monospace'>://</span> is protected and will not become an italic tag</td></tr>
		</table>
	</div>
	<?php
	if($eventMode == EventMode::modePreview)
	{
		echo '<h2>Preview</h2>';
		echo "<div id='noScriptInfo' style='display:block;'>";
		if(!empty($eventData['name']))
		{
			echo "<h2>".htmlspecialchars($eventData['name'])."</h2>";
		}else
		{
			echo "<h2>[Event Name]</h2>";
		}
		if(!empty($eventData['sdate']) && !empty($eventData['edate']) && !empty($eventData['stime']) && !empty($eventData['etime']))
		{
			if($eventData['sdate'] == $eventData['edate'])
			{
				echo "<small>".htmlspecialchars($eventData['stime'])." to ".htmlspecialchars($eventData['etime']);
				
			}else
			{
				echo "<small>".htmlspecialchars($eventData['sdate'])." @ ".htmlspecialchars($eventData['stime'])." to ".htmlspecialchars($eventData['edate'])." @ ".htmlspecialchars($eventData['etime']);
			}
			if(!empty($eventData['loc']))
			{
				echo " | ".htmlspecialchars($eventData['loc'])."</small>";
			}else
			{
				echo " | [Location]</small>";
			}
		}else
		{
			echo "<small>[Start Time] to [End Time] | [Location]</small>";
		}
		if(!empty($eventData['descript']))
		{
			echo '<div class="eventDesc">'.descParser(htmlspecialchars($eventData['descript'])).'</div>';
		}else
		{
			echo '<div class="eventDesc">[Description]</div>';
		}
		echo '</div>';
	}?>
</div>
<?php @include('includes/footer.inc.php'); ?>
