<?php
define('TITLE', "Gallery");
date_default_timezone_set("America/Vancouver");  ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>

	<link rel="stylesheet" type="text/css" href="resources/UberGallery.css" />
	<link rel="stylesheet" type="text/css" href="resources/colorbox/1/colorbox.css" />    
	<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.min.js"></script>
	<script type="text/javascript" src="resources/colorbox/jquery.colorbox.js"></script>
    <script type="text/javascript">
		$(document).ready(function(){
		    $("a[rel='colorBox']").colorbox({maxWidth: "90%", maxHeight: "90%", opacity: ".5"});
		});
	</script>
	<link rel="stylesheet" href="includes/style.css" type="text/css" />
<?php
@include('includes/header.inc.php');
include_once('resources/UberGallery.php');
$gallery = UberGallery::init()->createGallery('photos/');
@include('includes/footer.inc.php');
?>
