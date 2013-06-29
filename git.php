<?php 
	@include('includes/sessions.inc.php');
	define('TITLE', 'GIT');
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns='http://www.w3.org/1999/xhtml'>
	<head>
	<link rel='stylesheet' href='includes/style.css' type='text/css' />
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js" type='text/javascript'></script>
	<script type='text/javascript'>
function updateGitInfo() { 
	$.ajax({
	url: "https://api.github.com/repos/csss/csss.cs.sfu.ca/commits",
	content: document.body,
	success: function(data) {
		console.log(data);
		var text = "Current commit: " + data[0].sha + "<br />";
		$(github).html(text);
	}
	});
}
function pullGit() {
	$.ajax({
	url: "gitpull.php",
	content: document.body,
	success: function(data) {
		console.log(data);
		$(status_msg).html(data.status_msg);
		var text = "Current commit: " + data.sha + "<br />";
		$(local).html(text);
	}
	});
}
updateGitInfo();
	</script>

<?php @include('includes/header.inc.php'); ?>
<div id='content'>
	<h1>Git</h1>
<a href="#" onclick="pullGit()">Update</a>
<div id='github'>
	Loading
</div>
<div id='gitlocal'>
<?php
echo 'Current revision: ';
echo `git rev-parse HEAD`;
?>
</div>
<div id='status_msg'>
</div>
</div>
<?php @include('includes/footer.inc.php'); ?>
