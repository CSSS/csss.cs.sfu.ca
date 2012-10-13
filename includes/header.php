<?php
// Ensure we've been called internally
defined('CSSS') or die('This is an included page, and cannot be called by it self.');?>
 
    <title>
      <?php if(defined('TITLE')){ echo constant('TITLE') . " - "; } ?>Computing Science Student Society
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

  </head>
  <body>
    <div id="wrapper">
      <div id="header">
        <img src="images/header.jpg" alt="CSSS Logo" />
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
