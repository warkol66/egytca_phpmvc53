<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/classes/phpmvc/utils/Format.php,v 1.3 2006/02/22 07:28:41 who Exp $
* $Revision: 1.3 $
* $Date: 2006/02/22 07:28:41 $
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
* <p>Format is an abstract base class. Subclass this class to provide
* message formatting of messages, numbers and dates.
* <p>php.MVC provides a convienence message formatting class MessageFormat
* <p>Subclasss must implement the format() method to handle the actual
* parsing of specific messages.
* 
* @author John C. Wildenauer
* @version $Revision: 1.3 $
* @public
*/
class Format {

	// ----- Properties ----------------------------------------------------- //

	/**
	* The string pattern to be processed.
	* Eg: "Hello {0} World"
	* @private
	* @type string
	*/
	var $pattern = '';

	// ----- Constructors --------------------------------------------------- //

	/**
	* @param string	The pattern to be processed. Eg: "Hello {0} World"
	*/
	function Format($pattern) {

		$this->pattern = $pattern;

	}


	// ----- Public Methods ------------------------------------------------- //

	/**
	* Format the message pattern by replacing the {0}-{3} parameters
	* with the args array parameters.
	* <p>Abstract method.
	* <p>Returns the formatted message string.
	*
	* <p>Note: PHP seems picky about using method name format()
	*
	* @param array		args. The array of replacement parameters
	* @param string	arg0. The replacement for placeholder {0} in the message
	* @param string	arg1. The replacement for placeholder {1} in the message
	* @param string	arg2. The replacement for placeholder {2} in the message
	* @param string	arg3. The replacement for placeholder {3} in the message
	*
	* @public abstract
	* @returns string
	*/
	function formatMsg($args='', $arg0='', $arg1='', $arg2='', $arg3='') {
		//;
	}

}
?>