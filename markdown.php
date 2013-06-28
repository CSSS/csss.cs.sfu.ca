<?php
	@include('includes/sessions.inc.php'); // we don't really need authorization for this page, but there is no need to view it unless you are a user

	define('TITLE', 'Markdown Help');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns='http://www.w3.org/1999/xhtml'>
	<head>
	<link rel='stylesheet' href='includes/style.css' type='text/css' />
<?php @include('includes/header.inc.php'); ?>

<div id='content'>
	<h1>Markdown Help</h1>
	<h2>Bold Text</h2>
	<p style='font-family:monospace;'>**This text is bold.**</p>
	<div id='eventDesc'><p><b>This text is bold.</b></p></div>
	<h2>Italic Text</h2>
	<p style='font-family:monospace;'>//This text is italic.//</p>
	<div id='eventDesc'><p><i>This text is italic.</i></p></div>
	<h2>Underlined Text</h2>
	<p style='font-family:monospace;'>__This text is underlined.__</p>
	<div id='eventDesc'><p><u>This text is underlined.</u></p></div>
	<h2>Unordered Lists</h2>
	<p style='font-family:monospace;'>* This is an unordered list.<br/>* With multiple entries<br/>&nbsp;* And a sublist.</p>
	<div id='eventDesc'><ul><li>This is an unordered list.</li><li>With multiple entries</li><ul><li>And a sublist.</li></ul></ul></div>
	<h2>Ordered Lists</h2>
	<p style='font-family:monospace;'># This is an ordered list.<br/># Using numbers<br/>&nbsp;# and nested.<br/>@ Can also use lettered lists<br/>@ If you want to.</p>
	<div id='eventDesc'><ol><li>This is an ordered list.</li><li>Using numbers</li><ol><li>and nested.</li></ol></ol><ol type='a'><li>Can also use lettered lists</li><li>If you want to.</li></ol></div>
	<h2>Embedded Links</h2>
	<p style='font-family:monospace;'>We can also link to (Google)[http://www.google.ca].  But http://www.google.ca isn't highlighted, unless we do (http://www.google.ca)[http://www.google.ca].</p>
	<div id='eventDesc'><p>We can also link to <a href='http://www.google.ca'>Google</a>.  But http://www.google.ca isn't highlighted, unless we do <a href='http://www.google.ca'>http://www.google.ca</a>.</p></div>
	<h2>Notes</h2>
	<ul>
		<li><span style='font-family:monospace'>://</span> is protected and will not be converted into an italic tag.</li>
		<li>All characters are escaped and therefore html cannot be used.</li>
		<li>Although javascript can be used as a valid URL, please avoid using it.</li>
	</ul>
</div>
<?php @include('includes/footer.inc.php'); ?>
