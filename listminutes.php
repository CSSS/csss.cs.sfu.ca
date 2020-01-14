<?php

function dirList ($dir, $level = 0) 
{
	$results = array();
	$files = scandir($dir, $level==0); // If $level is 0 (top directory) sort DESC (parameter=1), otherwise sort ASC

	// Remove unwanted entries
	if (($idx = array_search('minutes_pre1990_unknown-01.png', $files)) !== FALSE) unset($files[$idx]);
	if (($idx = array_search('README.md', $files)) !== FALSE) unset($files[$idx]);
	$files = array_values($files);

	// Move subdirectories to the top of the list
	if ($level != 0) {
		for($i=count($files)-1, $j=0 ; $i >= $j ; $i--) {
			if (is_dir($dir.'/'.$files[$i])) {
				array_unshift($files, $files[$i]);	// Move directory to the front of the array
				unset($files[$i+1]);			// Remove it from it's current location to avoid duplicates ($i+1 because we've been shifted down 1)
				$i++;					// Indexes above current location have shifted down, correct this so we don't jump over the next file
				$j++;					// Directories that have been moved to the top must be ignored
			}
		}
	}

	foreach($files as $file){
		if ($file[0] == '.') {
			continue;
		}
		// if the file is actually a directory
		if (is_dir($dir.'/'.$file)) {
			$d = array('name' => $file);
			$list = dirList($dir.'/'.$file, $level+1);    // recursion
			$d['contents'] = $list;
			$results['dirs'][] = $d;
		} else {
			switch(strtolower(substr($file, -3, 3))) {
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
			$results['files'][] = array('name' => $file, 'thumb' => $thumbnail, 'path' => $dir.'/'.$file);
		}
	}
	return $results;
}

$files = dirList('minutes');

echo json_encode($files);


?>
