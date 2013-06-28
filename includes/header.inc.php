    <meta http-equiv='Content-Type' content='text/html;charset=utf-8' />
	<title>
      <?php if(defined('TITLE')){ echo constant('TITLE') . " - "; }else{ define('TITLE', ''); } ?>Computing Science Student Society
    </title>   
     
<script type="text/javascript">
<!--
function switchMenu(obj) {
var el = document.getElementById(obj);
if ( el.style.display != 'none' ) {
el.style.display = 'none';
}
else {
el.style.display = '';
}
}
//-->
</script>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-35197615-3', 'csss.cs.sfu.ca');
  ga('send', 'pageview');

</script>
  </head>
  <body>
    <div id="wrapper">
      <div id="header">
        <div id="headerLogo">
		  <div id="hoverWrapper">
			 <?php if(isset($_COOKIE['session']) && (constant('TITLE') != 'Logout'))
				{
					echo "<div id='loginLink'><a href='account.php'>Account</a> | <a href='logout.php'>Logout</a></div>";
				}else
				{
					echo "<div id='loginLink'><a href='login.php'>Login</a></div>";
				}
			 ?>
		  </div>
        </div>
	  </div>
      <div id="nav">
        <ul>
          <li>
            <a href="./">Home</a>
          </li>
          <li>
            <a href="gallery.php">Photo Gallery</a>
          </li>
          <li>
            <a href="http://csssfroshweek.ca/">Froshweek</a>
          </li>
        </ul>
      </div>
