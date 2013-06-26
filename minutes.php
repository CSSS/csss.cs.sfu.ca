<?php
// Allow access to page includes
define('CSSS', 1);
define('TITLE', "Minutes");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
  
  <link rel="stylesheet" href="includes/style.css" type="text/css" />
  
<?php @include('includes/header.php'); ?>
<div id="content">
	<h1>
		<a name="top">Minutes</a>
	</h1>
	<small>[<a href="#" onclick="expandAll(); return false">EXPAND ALL</a> | <a href="#" onclick="collapseAll(); return false">COLLAPSE ALL</a>]</small>
<?php

function dirList ($dir, $level = 0) 
{
	$files = scandir($dir, $level==0); // If $level is 0 (top directory) sort DESC (parameter=1), otherwise sort ASC

	// Remove unwanted entries
	if (($idx = array_search('minutes_pre1990_unknown-01.png', $files)) !== FALSE) unset($files[$idx]);
	$files = array_values($files);

	// Move subdirectories to the top of the list
	if ($level != 0)
	{
		for($i=count($files)-1, $j=0 ; $i >= $j ; $i--)
		{
			if (is_dir($dir.'/'.$files[$i]))
			{
				array_unshift($files, $files[$i]);	// Move directory to the front of the array
				unset($files[$i+1]);			// Remove it from it's current location to avoid duplicates ($i+1 because we've been shifted down 1)
				$i++;					// Indexes above current location have shifted down, correct this so we don't jump over the next file
				$j++;					// Directories that have been moved to the top must be ignored
			}
		}
	}

	foreach($files as $file) {
		if ($file[0] == '.') {
			continue;
		}
		// if the file is actually a directory
		if (is_dir($dir.'/'.$file)) 
		{	
			echo '<a onclick="switchMenu(\''.$file.'\');switchImage(\'image-'.$file.'\');return false"><h2><div id="image-'.$file.'" class="iconsprite closed">&nbsp;</div>'.$file.'</h2></a>'.PHP_EOL;
			echo '<div id="'.$file.'" class="folder" style="display: none">'.PHP_EOL;    // clickability
			echo '<ul style="list-style: none; margin-left: '.(20*$level).'">';    // indentation
			dirList($dir.'/'.$file, $level+1);    // recursion
			echo '<br /></ul>'.PHP_EOL.'</div>'.PHP_EOL;
		}
		else 
		{
			switch(strtolower(substr($file, -3, 3)))
			{
				case 'gif':
				case 'jpg':
				case 'jpeg':
				case 'bmp':
				case 'png':
					$thumbnail = 'png';
					break;
				case 'pdf':
					$thumbnail = 'pdf';
					break;
				case 'txt':
					$thumbnail = 'txt';
					break;
				default:
					$thumbnail = 'unknown';
			}
			echo '<li><div class="iconsprite '.$thumbnail.'">&nbsp;</div>';    // pretty little icons
			echo '<a href="http://csss.cs.sfu.ca/'.$dir.'/'.$file.'">'.$file.'</a></li>'.PHP_EOL;
		}
	}
}

dirList('minutes');

?>

	<small>[<a href="#top">TOP</a>]</small>
</div>

<script type="text/javascript">
<!-- START
var elements = new Array();
var tags = document.getElementsByTagName('*');

for(i=0,j=0 ; i < tags.length ; i++)
{
	if (tags[i].className == "folder")
		elements[j++] = tags[i];
}

function switchImage(elementID)
{
	var imgId = document.getElementById(elementID);
	if (imgId.className == "iconsprite closed") {
		imgId.className = "iconsprite open";
	} else {
		imgId.className = "iconsprite closed";
	}
}

function expandAll()
{
	for(i=0,j=elements.length ; i < j ; i++)
	{
		elements[i].style.display = '';
		document.getElementById('image-' + elements[i].id).src = 'images/icons/folderopen.gif';
	}
}

function collapseAll()
{
        for(i=0,j=elements.length ; i < j ; i++)
        {
                elements[i].style.display = 'none';
		document.getElementById('image-' + elements[i].id).src = 'images/icons/folder.gif';
        }
}
// END -->
</script>

<?php
@include('includes/footer.php');
?>
