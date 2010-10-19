<?php
/*
* Smarty plugin
*
-------------------------------------------------------------
* File: modifier.smarty_modifier_mb_truncate_strip_tags.php
* Type: modifier
* Name: smarty_modifier_mb_truncate_strip_tags
* Version: 1.0
* Date: June 19th, 2003
* Purpose: Cut a string preserving any tag nesting and matching.
* Install: Drop into the plugin directory.
* Author: Original Javascript Code: Benjamin Lupu <lupufr@aol.com>
* Translation to PHP & Smarty: Edward Dale <scompt@scompt.com>
* Modification to add a string: Sebastian Kuhlmann <sebastiankuhlmann@web.de>
*
-------------------------------------------------------------
*/
function smarty_modifier_mb_truncate_strip_tags($string, $length = 300, $etc = '...', $addstring="", $charset='UTF-8')
{

	if (mb_strlen(strip_tags($string),$charset) > $length) {

		if(!empty($string) && $length > 0) {
			$isText = true;
			$ret = "";
			$i = 0;

			$currentChar = "";
			$lastSpacePosition = -1;
			$lastChar = "";

			$tagsArray = array();
			$currentTag = "";
			$tagLevel = 0;

			$noTagLength = mb_strlen(strip_tags($string),$charset);

			// Parser loop
			for($j=0; $j < mb_strlen($string,$charset); $j++) {
			
				$currentChar = mb_substr($string, $j, 1,$charset);
				$ret .= $currentChar;
				
				// Lesser than event
				if($currentChar == "<") 
					$isText = false;
				
				// Character handler
				if($isText) {
	
					// Memorize last space position
					if($currentChar == " ") 
						$lastSpacePosition = $j;
					else
						$lastChar = $currentChar; 
					
					$i++;
				} 
				else
					$currentTag .= $currentChar;

				// Greater than event
				if($currentChar == ">") {
					$isText = true;
				
					// Opening tag handler
					if ((strpos($currentTag, "<") !== FALSE) && (mb_strpos($currentTag, "/>") === FALSE) && (mb_strpos($currentTag, "</") === FALSE)) {
						// Tag has attribute(s)
						if(strpos($currentTag, " ") !== FALSE)
							$currentTag = mb_substr($currentTag, 1, mb_strpos($currentTag, " ") - 1, $charset);
						else
							// Tag doesn't have attribute(s)
							$currentTag = mb_substr($currentTag, 1, -1, $charset);	
						array_push($tagsArray, $currentTag);
					} 
					else if(mb_strpos($currentTag, "</") !== FALSE)
						array_pop($tagsArray);
			
					$currentTag = "";
				}

				if($i >= $length)
					break;
			}

			// Cut HTML string at last space position
			if($length < $noTagLength) {
				if($lastSpacePosition != -1)
					$ret = mb_substr($string, 0, $lastSpacePosition, $charset);
				else
					$ret = mb_substr($string, $j);
			}
	
			// Close broken XHTML elements
			while(sizeof($tagsArray) != 0) {
				$aTag = array_pop($tagsArray);
				$ret .= "</" . $aTag . ">\n";
			}

		} 
		else
			$ret = "";

		return($ret.$addstring);
	}
	else
		return ($string);
}
