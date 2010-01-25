<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/classes/phpmvc/action/ActionMessage.php,v 1.3 2006/02/22 06:54:19 who Exp $
* $Revision: 1.3 $
* $Date: 2006/02/22 06:54:19 $
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
* <p>An encapsulation of an individual message returned by the
* <code>validate()</code> method of an <code>ActionForm</code>, consisting
* of a message key (to be used to look up message text in an appropriate
* message resources database) plus up to four placeholder objects that can
* be used for parametric replacement in the message text.</p>
*
* @author John C Wildenauer (php.MVC port)<br>
*  Credits:<br>
*  Craig R. McClanahan (Jakata Struts: see jakarta.apache.org)<br>
*  David Winterfeldt (Jakata Struts: see jakarta.apache.org)
* @version $Revision: 1.3 $
* @public
*/
class ActionMessage {

	// ----- Protected Instance Variables ----------------------------------- //

	/**
	* The message key for this message.
	* @private
	* @type string
	*/
	var $key = NULL;


	/**
	* The replacement values for this mesasge.
	* @private
	* @type array
	*/
	var $values = array();


	// ----- Constructors --------------------------------------------------- //

	/**
	* Construct an action message with the specified replacement values.
	*
	* @param string The Message key for this message
	* @param string First replacement value [optional]
	* @param string Second replacement value [optional]
	* @param string Third replacement value [optional]
	* @param string Fourth replacement value [optional]
	* @param string Array of replacement values [optional]
	* @public
	* @returns void
	*/
	function ActionMessage($key, $value0='', $value1='',
									$value2='', $value3='', $values='') {

		$this->key = $key;
		#this.values = new Object[] { value0, value1, value2, value3 };

		// Action message with no replacement values
		$this->values = NULL;
		
		// Action message with the specified replacement values
		if($value0 != '') {
			$this->values[] = $value0;
		}
		if($value1 != '') {
			$this->values[] = $value1;
		}
		if($value2 != '') {
			$this->values[] = $value2;
		}
		if($value3 != '') {
			$this->values[] = $value3;
		}
		if($values != '' && is_array($values)) {
			$this->values = NULL; // reset the values array !!!!!!!!!!
			$this->values = $values;
		}

	}


	// ----- Public Methods ------------------------------------------------- //

	/**
	* Get the message key for this message.
	*
	* @public
	* @returns string
	*/
	function getKey() {

		return $this->key;

	}

	
	/**
	* Get the replacement values for this message.
	*
	* @public
	* @returns  array
	*/
	function getValues() {

		return $this->values;

	}

}
?>