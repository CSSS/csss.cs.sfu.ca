<?php
// Allow access to page includes
define('CSSS', 1);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
  
  <link rel="stylesheet" href="includes/style.css" type="text/css" />
  
<?php @include('includes/header.php'); ?>
<div id="main">
  <h1>
	Computing Science Student Society
  </h1>
  <p>
	The Computing Science Student Society (CSSS) promotes the interests of
	students in the <a href="http://www.cs.sfu.ca" target="_blank">School of Computing
	Science</a> at <a href="http://www.sfu.ca" target="_blank">Simon Fraser University</a>.
	It is a descendant organization of the <a href=
	"http://www.sfss.ca" target="_blank">Simon Fraser Student Society</a>.
  </p>
  <p>
	The CSSS common room is located in room 9802 in the Applied Sciences Building, 
	just off the atrium and next to CSIL. It offers $1.00
	pop, 5 cent photocopying, a free phone, and comfy couches. The
	common room is open to all students.
  </p>
  <p>
	The CSSS holds end of semester socials, along with
	other events throughout each semester. CSSS meetings are held several
	times a semester and are announced to the <a href=
	"mailto:csss-announce@sfu.ca">csss-announce</a> and <a href=
	"mailto:cmpt-students@sfu.ca">cmpt-students</a> mailing lists as 
	well as on posters around CSIL and the ASB. Each fall the CSSS holds
	<a href="http://csssfroshweek.ca">FroshWeek</a> to welcome new
	Computing Science students.
  </p>
  <p>
	For more information about events or other aspects of the CSSS, 
	subscribe to some of the mailing lists below, or see the notice 
	board located just outside the common room. If you are interested 
	in being more involved with the CSSS, join us at one of our meetings 
	or stop by the common room.
  </p>
  <h1>
    Current Executives
  </h1>
  <p>
    Kenneth Kwok - <b>President</b><br/>
    Nicholas Hoekstra - <b>Vice-President</b><br/>
    Allan Saravi - <b>Treasurer</b><br/>
    Laura Antonescu - <b>Director of Resources</b><br/>
    Kyle Chutskoff - <b>Director of Activities</b><br/>
    Paul Allan - <b>Secretary</b><br/>
    Jeremy Lo - <b>Executive at Large</b><br/>
	Claudia Kam - <b>First Year Representative</b><br/>
	Siddhant Agrawal - <b>First Year Representative</b>
  </p>
  <h1>
	Mailing Lists
  </h1>
  <p>
	<a href="mailto:cmpt-students@sfu.ca">cmpt-students</a><br />
	List for announcements from the CSSS. All registered CMPT students are
	on this list.<br />
	<br />
	<a href="mailto:csss-announce@sfu.ca">csss-announce</a><br />
	Moderated announcements pertaining to the CSSS. This is the best list
	for anyone casually interested in CSSS events.<br />
	<br />
	<a href="mailto:csss-active@sfu.ca">csss-active</a><br />
	Discussion by and for people actively involved in the CSSS. Discussion
	on announcements posted to csss-announce may be carried out here. This
	list receives all mail to csss-announce, so there is no need to be
	subscribed to both lists.<br />
	<br />
	<a href="mailto:csss-exec@sfu.ca">csss-exec</a><br />
	For discussion among the CSSS executive.<br />
	<br />
	<a href="mailto:csss-seminar@sfu.ca">cmpt-seminar</a><br />
	For announcements of seminars and talks relating to computing
	topics.<br />
	<br />
	<a href="mailto:csss-jobs@sfu.ca">cmpt-jobs</a><br />
	For job postings that may be of interest to Computing Science students.
	Employers may send computing related job openings to
	csss-cmpt-jobs-manager@sfu.ca.
	<br />
	<br />
	To subscribe or unsubscribe from a mailing list, search for the list using the <a href="https://maillist.sfu.ca">web interface</a>, or use the <code>maillist</code> command on fraser.
  </p>
  <h1>
	Volunteering
  </h1>
  <p>
	A list of the CSSS Volunteer of the Year recipients since 2009 can be found 
	<a href="awards.php">here</a>.
  </p>
  <p>
If you are interested in volunteering with the CSSS, please email 
<a href="mailto:csss-exec@sfu.ca">csss-exec@sfu.ca</a> for more information.
  </p>
  
</div>
<div id="sidebar">
 <div class="box">
	<h1>
	  Groups
	</h1>
	<p>
		<a href="https://www.facebook.com/groups/2203105681/"><img src="images/facebook-icon.png" alt="Join us on Facebook!" width="40px" height="40px"/></a>
		<a href="http://www.reddit.com/r/commonroom/"><img src="images/reddit-icon.png" alt="Follow our subreddit!" width="40px" height="40px"/></a>
		<a href="http://steamcommunity.com/groups/sfucsss"><img src="images/steam-icon.png" alt="Join our Steam Group!" width="40px" height="40px"/></a>
		<a href="http://twitter.com/#!/SFUpop"><img src="images/twitter-icon.png" alt="Follow our Pop Machine!" width="40px" height="40px"/></a>
	</p>
	<hr />
	<p>
		Connect with us!
	</p>
  </div>
  <div class="box">
	<h1>CUTC Infect</h1>
	<a href='http://infect.cutc.ca/' target='_blank'><img src='images/CUTC-Logo.png' /></a>
  </div>
  <!-- BEGIN EVENTS -->
  <?php @include("includes/events.php"); ?>
  <!-- END EVENTS -->
  <div class="box">
	<h1>
	  Minutes
	</h1>
	<p>
	  The minutes from all Computing Science Student Society meetings can
	  be found <a href="minutes.php">here</a>.
	</p>
  </div>
  <div class="box">
	 <h1>
	Constitution
  </h1>
  <p>
	The constitution of the CSSS is available <a href=
	"constitution.php">here</a>.
  </p>
  </div>
  <div class="box"><a name="contact"></a>
	<h1>
	  Contact Information
	</h1>
	<p>
	  Web: http://csss.cs.sfu.ca<br />
	  Email: csss-exec@sfu.ca<br />
	  Office: ASB 9802<br />
	  Sysadmin: Joel Teichroeb<br />
	</p>
  </div>
  <div class="box">
	<h1>
	  Mailing Address
	</h1>
	<p>
	  Computing Science Student Society<br />
	  c/o School of Computing Science<br />
	  ASB 9971 - 8888 University Drive<br />
	  Burnaby, BC<br />
	  V5A 1S6
	</p>
  </div>
</div>
<?php
@include('includes/footer.php');
?>
