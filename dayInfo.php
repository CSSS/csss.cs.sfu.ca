<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
  
	<link rel="stylesheet" href="includes/style.css" type="text/css" />
	<link rel="stylesheet" href="includes/calendar.css" type="text/css" />
	
	<?php 
	
	$titleYear = "";
	$titleMonth = "";
	$titleDay = "";
	
	if(isset($_GET["year"]))
	{
		$titleYear = $_GET["year"];
	}	
	if(isset($_GET["month"]))
	{
		$titleMonth = $_GET["month"];
	}
	
	if(isset($_GET["day"]))
	{
		$titleDay = $_GET["day"];
	}
	
	define('TITLE', $titleDay."/".$titleMonth."/".$titleYear." Events");
	
	@include('includes/header.inc.php');
	echo '<div id="noScriptInfo">';
	@include('includes/dayDetails.php');
	
	echo '<br/><a href="calendar.php?year='.$titleYear.'&month='.$titleMonth.'">&larr; Go Back</a>';
	echo '</div>';
	
	@include('includes/footer.inc.php');
	?>
	
	
?>
