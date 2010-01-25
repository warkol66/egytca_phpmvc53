<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/classes/phpmvc/utils/MessageFormat.php,v 1.3 2006/02/22 08:17:34 who Exp $
* $Revision: 1.3 $
* $Date: 2006/02/22 08:17:34 $
*
* ====================================================================
*
* License:	GNU Lesser General Public License (LGPL)
*
* Copyright (c) 2002-2006 John C.Wildenauer.  All rights reserved.
*
* This file is part of the php.MVC Web applications framework
*
* This library is free software; you can redistribute it and/or
* modify it under the terms of the GNU Lesser General Public
* License as published by the Free Software Foundation; either
* version 2.1 of the License, or (at your option) any later version.

* This library is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
* Lesser General Public License for more details.

* You should have received a copy of the GNU Lesser General Public
* License along with this library; if not, write to the Free Software
* Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/


/**
* <p>MessageFormat is a concrete format class to handle the actual
* parsing of specific messages.
*
* @author John C. Wildenauer
* @version $Revision: 1.3 $
* @public
*/
class MessageFormat extends Format {

	// ----- Constructors --------------------------------------------------- //

	/**
	* @param string	The pattern to be processed. Eg: "Hello {0} World"
	*/
	function MessageFormat($pattern) {

		// Build the base class first
		parent::Format($pattern);

	}


	// ----- Public Methods ------------------------------------------------- //

	/**
	* <p>Format the message pattern by replacing the {0}-{3} parameters
	* with the corresponding args array parameters. Returns the formatted 
	* message string.
	*
	* <p>Usage: call the MessageFormat constructor with the pattern to
	* process as the argument. 
	* Eg: new MessageFormat("This is my {0} post for week {1}"). 
	* Then call the format method with the replacement parameters in the 
	* args array Eg: $format->format( array('First', 'Five') ).
	* 
	* Development note: Check strtr(...), may be faster then str_replace() !
	*   $trans = array("hello" => "hi", "hi" => "hello");
	*   echo strtr("hi all, I said hello", $trans) . "\n";
	*   This will show: "hello all, I said hi", 
	*
	* @param array		args. The array of replacement parameters [0-3].
	* @param string	arg0. The replacement for placeholder {0} in the message
	* @param string	arg1. The replacement for placeholder {1} in the message
	* @param string	arg2. The replacement for placeholder {2} in the message
	* @param string	arg3. The replacement for placeholder {3} in the message
	* @public
	* @returns string
	*/
	function formatMsg($args='', $arg0='', $arg1='', $arg2='', $arg3='') {

		// No array of arguments or string arguments
		if( !is_array($args) && $arg0=='' && $arg1=='' && $arg2=='' && $arg3=='' )
			return $this->pattern;

		// If we have string argments
		if( (!is_array($args)) && ($arg0!=''||$arg1!=''||$arg2!=''||$arg3!='') ) {
			if($arg0 != '')
				$args[0] = $arg0;
			if($arg1 != '')
				$args[1] = $arg1;	
			if($arg2 != '')
				$args[2] = $arg2;	
			if($arg3 != '')
				$args[3] = $arg3;	
		}

		// Problem with the message pattern string!
		if( is_string($this->pattern) && strlen($this->pattern) > 0 ) {
			$pattern = $this->pattern;
		} else {
			return "Opps, we got a problem with the message pattern";
		}

		$params = array('{0}', '{1}', '{2}', '{3}');

		foreach($args as $key => $val) {
			$pattern = str_replace($params[$key], $val, $pattern);
		}

		return $pattern;

	}

}
?>