<?php
function descParser($desc)
{
	$const_newLine = '<br />';
	$const_startOrdered = '<ol>';
	$const_endOrdered = '</ol>';
	$const_startUnordered = '<ul>';
	$const_endUnordered = '</ul>';
	$const_startLettered = '<ol type="a">';
	$const_endLettered = '</ol>';
	$const_startElement = '<li>';
	$const_endElement = '</li>';
	$const_startBold = '<span style="font-weight:bold;">';
	$const_endBold = '</span>';
	$const_startUnderline = '<span style="text-decoration:underline;">';
	$const_endUnderline = '</span>';
	$const_startItalic = '<span style="font-style:italic;">';
	$const_endItalic = '</span>';
	$const_startUrl = '<a href="';
	$const_middleUrl = '">';
	$const_endUrl = '</a>';


	// u = underline
	// b = bold
	// i = italic
	// l = unordered list
	// n = ordered list
	// s = list element
	// a = lettered
	$arrMarkup = array();

	$output = '';

	$inLength = strlen($desc);

	for($index = 0; $index < $inLength; $index++)
	{
		$char2 = substr($desc, $index, 2);
		if($char2 == '__')
		{
			if(end($arrMarkup) && end($arrMarkup) == 'u')
			{
				$output .= $const_endUnderline;
				array_pop($arrMarkup);
				$index += 1;
			}else
			{
				$output .= $const_startUnderline;
				array_push($arrMarkup, 'u');
				$index += 1;
			}
		}elseif($char2 == '**')
		{
			if(end($arrMarkup) && end($arrMarkup) == 'b')
			{
				$output .= $const_endBold;
				array_pop($arrMarkup);
				$index += 1;
			}else
			{
				$output .= $const_startBold;
				array_push($arrMarkup, 'b');
				$index += 1;
			}
		}elseif(substr($desc, $index, 3) == '://')
		{
			$output .= '://';
			$index += 2;
		}elseif($char2 == '//' )
		{
			if(end($arrMarkup) && end($arrMarkup) == 'i')
			{
				$output .= $const_endItalic;
				array_pop($arrMarkup);
				$index += 1;
			}else
			{
				$output .= $const_startItalic;
				array_push($arrMarkup, 'i');
				$index += 1;
			}
		}elseif(substr($desc, $index, 1) == '(')
		{
			$urlTextStart = $index+1;
			$urlTextLength = 0;
			while($index < $inLength && substr($desc, $urlTextStart+$urlTextLength, 1) != ')' && substr($desc, $urlTextStart+$urlTextLength, 1) != "\n")
			{
				$urlTextLength += 1;
			}
			if(substr($desc, $urlTextStart+$urlTextLength, 2) == ')[')
			{
				$urlLinkStart = $urlTextStart+$urlTextLength+2;
				$urlLinkLength = 0;
				while($index < $inLength && substr($desc, $urlLinkStart+$urlLinkLength, 1) != ']' && substr($desc, $urlLinkStart+$urlLinkLength, 1) != "\n")
				{
					$urlLinkLength += 1;
				}
				if(substr($desc, $urlLinkStart+$urlLinkLength, 1) == ']')
				{
					// valid url tag
					$output .= $const_startUrl . substr($desc, $urlLinkStart, $urlLinkLength) . $const_middleUrl . substr($desc, $urlTextStart, $urlTextLength) . $const_endUrl;
					$index = $urlLinkStart + $urlLinkLength;
				}else // not a valid url tag
				{
					$output .= '(';
				}
			}else
			{ // not a valid url tag
				$output .= '(';
			}
		}elseif($index == 0 && (substr($desc, $index, 1) == ' ' || $char2 == '* ' || $char2 == '- ' || $char2 == '# ' || $char2 == '@ '))
		{
			$listIndent = 0;
			while(substr($desc, $listIndent, 1) == ' ')
			{
				$listIndent += 1;
			}
			$char2 = substr($desc, $listIndent, 2);
			if($char2 == '* ' || $char2 == '- ')
			{
				for($i = 0; $i <= $listIndent; $i++)
				{
					$output .= $const_startUnordered;
					array_push($arrMarkup, 'l');
				}
				$output .= $const_startElement;
				array_push($arrMarkup, 's');
				$index += $listIndent + 1;
			}else if($char2 == '# ')
			{
				for($i = 0; $i <= $listIndent; $i++)
				{
					if($i == $listIndent)
					{
						$output .= $const_startOrdered;
						array_push($arrMarkup, 'n');
					}else
					{
						$output .= $const_startUnordered;
						array_push($arrMarkup, 'l');
					}
				}
				$output .= $const_startElement;
				array_push($arrMarkup, 's');
				$index += $listIndent + 1;
			}else if($char2 == '@ ')
			{
				for($i = 0; $i <= $listIndent; $i++)
				{
					if($i == $listIndent)
					{
						$output .= $const_startLettered;
						array_push($arrMarkup, 'a');
					}else
					{
						$output .= $const_startUnordered;
						array_push($arrMarkup, 'l');
					}
				}
				$output .= $const_startElement;
				array_push($arrMarkup, 's');
				$index += $listIndent + 1;

			}else
			{
				$output .= ' ';
			}
		}elseif(substr($desc, $index, 1) == "\n")
		{
			if(in_array('s', $arrMarkup, true)) // we have an s in here
			{
				while(end($arrMarkup) && end($arrMarkup) != 's') // look for our element tag
				{
					switch(array_pop($arrMarkup))
					{
						case 'b':
							$output .= $const_endBold;
							break;

						case 'i':
							$output .= $const_endItalic;
							break;

						case 'u':
							$output .= $const_endUnderline;
							break;
					}
				}
				$output .= $const_endElement;
				array_pop($arrMarkup);
			}
			// need to change this 
			$listIndent = 0;
			while(substr($desc, $index+1+$listIndent, 1) == ' ')
			{ // check the number of lists we want
				$listIndent += 1;
			}
			$next2 = substr($desc, $index+1+$listIndent, 2); //and see if we need a list
			if($next2 == '* ' || $next2 == '- ')
			{
				$listIndent += 1;
				$tagCount = array_count_values($arrMarkup);
				$curListIndent = 0;
				if(array_key_exists('l', $tagCount))
				{
					$curListIndent += $tagCount['l'];
				}
				if(array_key_exists('n', $tagCount))
				{
					$curListIndent += $tagCount['n'];
				}
				if(array_key_exists('a', $tagCount))
				{
					$curListIndent += $tagCount['a'];
				}
				while($curListIndent < $listIndent)
				{
					$output .= $const_startUnordered;
					array_push($arrMarkup, 'l');
					$curListIndent += 1;
				}
				while($curListIndent > $listIndent)
				{
					if(end($arrMarkup) == 'l')
					{
						$output .= $const_endUnordered;
						array_pop($arrMarkup);
					}else if(end($arrMarkup) == 'a')
					{
						$output .= $const_endLettered;
						array_pop($arrMarkup);
					}else
					{
						$output .= $const_endOrdered;
						array_pop($arrMarkup);
					}
					$curListIndent -= 1;
				}
				if(end($arrMarkup) != 'l')
				{
					if(end($arrMarkup) == 'n')
					{
						$output .= $const_endOrdered;
						array_pop($arrMarkup);
					}else
					{
						$output .= $const_endLettered;
						array_pop($arrMarkup);
					}
					$output .= $const_startUnordered;
					array_push($arrMarkup, 'l');
				}
				$output .= $const_startElement;
				array_push($arrMarkup, 's');
				$index = $index + $listIndent + 1;
			}else if($next2 == '# ')
			{
				$listIndent += 1;
				$tagCount = array_count_values($arrMarkup);
				$curListIndent = 0;
				if(array_key_exists('l', $tagCount))
				{
					$curListIndent += $tagCount['l'];
				}
				if(array_key_exists('n', $tagCount))
				{
					$curListIndent += $tagCount['n'];
				}
				if(array_key_exists('a', $tagCount))
				{
					$curListIndent += $tagCount['a'];
				}
				while($curListIndent < $listIndent)
				{
					if($curListIndent + 1 == $listIndent)
					{
						$output .= $const_startOrdered;
						array_push($arrMarkup, 'n');
					}else
					{
						$output .= $const_startUnordered;
						array_push($arrMarkup, 'l');
					}
					$curListIndent += 1;
				}
				while($curListIndent > $listIndent)
				{
					if(end($arrMarkup) == 'l')
					{
						$output .= $const_endUnordered;
						array_pop($arrMarkup);
					}else if(end($arrMarkup) == 'n')
					{
						$output .= $const_endOrdered;
						array_pop($arrMarkup);
					}else
					{
						$output .= $const_endLettered;
						array_pop($arrMarkup);
					}
					$curListIndent -= 1;
				}
				if(end($arrMarkup) != 'n')
				{
					if(end($arrMarkup) == 'l')
					{
						$output .= $const_endUnordered;
						array_pop($arrMarkup);
					}else
					{
						$output .= $const_endLettered;
						array_pop($arrMarkup);
					}
					$output .= $const_startOrdered;
					array_push($arrMarkup, 'n');
				}
				$output .= $const_startElement;
				array_push($arrMarkup, 's');
				$index = $index + $listIndent + 1;
			}else if($next2 == '@ ')
			{
				$listIndent += 1;
				$tagCount = array_count_values($arrMarkup);
				$curListIndent = 0;
				if(array_key_exists('l', $tagCount))
				{
					$curListIndent += $tagCount['l'];
				}
				if(array_key_exists('n', $tagCount))
				{
					$curListIndent += $tagCount['n'];
				}
				if(array_key_exists('a', $tagCount))
				{
					$curListIndent += $tagCount['a'];
				}
				while($curListIndent < $listIndent)
				{
					if($curListIndent + 1 == $listIndent)
					{
						$output .= $const_startLettered;
						array_push($arrMarkup, 'n');
					}else
					{
						$output .= $const_startUnordered;
						array_push($arrMarkup, 'l');
					}
					$curListIndent += 1;
				}
				while($curListIndent > $listIndent)
				{
					if(end($arrMarkup) == 'l')
					{
						$output .= $const_endUnordered;
						array_pop($arrMarkup);
					}else if(end($arrMarkup) == 'a')
					{
						$output .= $const_endLettered;
						array_pop($arrMarkup);
					}else
					{
						$output .= $const_endOrdered;
						array_pop($arrMarkup);
					}
					$curListIndent -= 1;
				}
				if(end($arrMarkup) != 'a')
				{
					if(end($arrMarkup) == 'l')
					{
						$output .= $const_endUnordered;
						array_pop($arrMarkup);
					}else
					{
						$output .= $const_endOrdered;
						array_pop($arrMarkup);
					}
					$output .= $const_startLettered;
					array_push($arrMarkup, 'a');
				}
				$output .= $const_startElement;
				array_push($arrMarkup, 's');
				$index = $index + $listIndent + 1;
			}else if(in_array('l', $arrMarkup, true) || in_array('n', $arrMarkup, true) || in_array('a', $arrMarkup, true))
			{
				while(end($arrMarkup) && (end($arrMarkup) == 'l' || end($arrMarkup) == 'n') || end($arrMarkup) == 'a')
				{
					if(end($arrMarkup) == 'l')
					{
						$output .= $const_endUnordered;
						array_pop($arrMarkup);
					}else if(end($arrMarkup) == 'n')
					{
						$output .= $const_endOrdered;
						array_pop($arrMarkup);
					}else
					{
						$output .= $const_endLettered;
						array_pop($arrMarkup);
					}
				}
			}else{
				$output .= $const_newLine;
			}
		}else
		{
			$output .= substr($desc, $index, 1);
		}
	}
	while(count($arrMarkup) != 0)
	{
		switch(array_pop($arrMarkup))
		{
			case 'u':
				$output .= $const_endUnderline;
				break;
			
			case 'b':
				$output .= $const_endBold;
				break;

			case 'i':
				$output .= $const_endItalic;
				break;

			case 's':
				$output .= $const_endElement;
				break;

			case 'l':
				$output .= $const_endUnordered;
				break;

			case 'n':
				$output .= $const_endOrdered;
				break;

			case 'a':
				$output .= $const_endLettered;
				break;
		}
	}
	return $output;
}
?>
