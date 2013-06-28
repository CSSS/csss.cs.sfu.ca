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
function setCommit(commit) {
	var text = "Current commit: " + commit + "<br />";
	$(git).html(text);
}
function updateGitInfo() { 
	$.ajax({
	url: "https://api.github.com/repos/csss/csss.cs.sfu.ca/commits",
	content: document.body,
	success: function(data) {
		console.log(data);
		setCommit(data[0].sha);
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
		setCommit(data.sha);
		
	}
	});
}
updateGitInfo();
	</script>

<?php @include('includes/header.inc.php'); ?>
<div id='content'>
	<h1>Git</h1>
<a href="#" onclick="pullGit()">Update</a>
<div id='git'>
	Loading
</div>
<?php
echo 'Current revision: ';
echo `git rev-parse HEAD`;
echo '<br />';
?>
<div id='status_msg'>
</div>
</div>
<?php @include('includes/footer.inc.php'); ?>
