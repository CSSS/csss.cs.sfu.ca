<?php 
	@include('includes/sessions.inc.php');
	@include('includes/descParser.inc.php');
	define('TITLE', 'GIT');
	
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
	<script type='text/javascript'> 
$.ajax({
url: "https://api.github.com/repos/csss/csss.cs.sfu.ca/commits",
content: document.body,
success: function(data) {
	var text = "";
	text = "Current commit: " + data[0].sha + "<br />";

	$(git).html(text);

}
});
	</script>

<?php @include('includes/header.inc.php'); ?>
<div id='content'>
	<h1>Git</h1>

<?php
if (isset($_GET['update'])) {
	echo `git pull`;
	echo '<br />';
	echo `git submodule update`;
} else {
	echo '<a href="git.php?update">Update</a>';
}
?>
<div id='git'>
	Loading
</div>
<?php
echo 'Current revision: ';
echo `git rev-parse HEAD`;
echo '<br />';
?>
</div>
<?php @include('includes/footer.inc.php'); ?>
