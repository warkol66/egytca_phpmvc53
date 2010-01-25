<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/classes/phpmvc/action/ActionError.php,v 1.4 2006/02/24 06:54:50 who Exp $
* $Revision: 1.4 $
* $Date: 2006/02/24 06:54:50 $
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
* <p>An encapsulation of an individual error message returned by the
* <code>validate()</code> method of an <code>ActionForm</code>, consisting
* of a message key (to be used to look up message text in an appropriate
* message resources database) plus up to four placeholder objects that can
* be used for parametric replacement in the message text.</p>
*
* <p>The placeholder objects are referenced in the message text using the same
* syntax used by the JDK <code>MessageFormat</code> class. Thus, the first
* placeholder is '{0}', the second is '{1}', etc.</p>
*
* @author John C Wildenauer (php.MVC port)<br>
*  Credits:<br>
*  Craig R. McClanahan (Jakata Struts: see jakarta.apache.org)
* @version $Revision: 1.4 $
* @public
*/
class ActionError extends ActionMessage {

	// ----- Constructors --------------------------------------------------- //

	/**
	* Construct an action error with optional replacement values.
	*
	* @param string	Message key for this error message, with optional
	*						replacement values
	* @param string	First replacement value [optional]
	* @param string	Second replacement value [optional]
	* @param string	Third replacement value [optional]
	* @param string	Fourth replacement value [optional]
	* @param array		Array of replacement values [optional]
	* @public
	* @returns void
	*/
	function ActionError($key, $value0='', $value1='',
                       	$value2='', $value3='', $values='') {

		// Setup the parent constructor first 
		parent::ActionMessage($key, $value0, $value1, 
									$value2, $value3, $values);

    }

}
?>