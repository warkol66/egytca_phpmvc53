<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/classes/phpmvc/action/ActionMessages.php,v 1.6 2006/02/22 06:54:54 who Exp $
* $Revision: 1.6 $
* $Date: 2006/02/22 06:54:54 $
*
* ====================================================================
*
* License:	GNU Lesser General Public License (LGPL)
*
* Copyright (c) 2003-2006 John C.Wildenauer.  All rights reserved.
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
* <p>A class that encapsulates messages.  Messages can be either global
* or they are specific to a particular bean property.</p>
*
* <p>Each individual message is described by an <code>ActionMessage</code>
* object, which contains a message key (to be looked up in an appropriate
* message resources database), and up to four placeholder arguments used for
* parametric substitution in the resulting message.</p>
*
* <p><strong>IMPLEMENTATION NOTE</strong> - It is assumed that these objects
* are created and manipulated only within the context of a single thread.
* Therefore, no synchronization is required for access to internal
* collections.</p>
*
* @author John C Wildenauer (php.MVC port)<br>
*  Credits:<br>
*  David Geary (Jakata Struts: see jakarta.apache.org)<br>
*  Craig R. McClanahan (Jakata Struts: see jakarta.apache.org)<br>
*  David Winterfeldt (Jakata Struts: see jakarta.apache.org)
* @version $Revision: 1.6 $
* @public
*/
class ActionMessages {

	// ----- Manifest Constants --------------------------------------------- //

	/**
	* The "property name" marker to use for global messages, as opposed to
	* those related to a specific property.
	* @public
	* @type string
	*/
	var $GLOBAL_MESSAGE = "phpmvc.action.GLOBAL_MESSAGE"; // public static final


	// ----- Protected Instance Variables ----------------------------------- //

	/**
	* The accumulated set of <code>ActionMessage</code> objects (represented
	* as an ArrayList) for each property, keyed by property name.
	* @private
	* @type array
	*/
	var $messages = array();	// new HashMap()

	/**
	* The current number of the property/key being added.  This is used
	* to maintain the order messages are added.
	* @private
	* @type int
	*/
	var $iCount = 0;


	// ----- Public Methods ------------------------------------------------- //

	/**
	* Add a message to the set of messages for the specified property.  An
	* order of the property/key is maintained based on the initial addition
	* of the property/key.
	*
	* @param string  Property name (or ActionMessages.GLOBAL_MESSAGE)
	* @param ActionMessage The message to be added
	*
	* @public
	* @returns void 
	*/
	function add($property, $message) {
		
		// TO-DO TEST CASE
		if( array_key_exists($property, $this->messages) ) {
			$ami =&  $this->messages[$property]; // ActionMessageItem
		} else {
			$ami = NULL;
		}


		$list = NULL;	// List

		if($ami == NULL) { 		// no ActionMessageItem
			$list = array(); 		// new ArrayList()
			$list[] = $message;	// add this message to the new list array
			$ami = new ActionMessageItem($list, $this->iCount++);
			$this->messages[$property] = $ami;
		} else {
			$list =& $ami->getList(); // return a reference to list !!
			// Add this message to the existing $ami->list array
			$list[] = $message;	
		}

	}


	/**
	* Clear all messages recorded by this object.
	*
	* @public
	* @returns void
	*/
	function clear() {

		$this->messages = array();

	}


	/**
	* Return <code>true</code> if there are no messages recorded
	*  in this collection, or <code>false</code> otherwise.
	* Note: empty() is a PHP reserved function name (original Strust
	*  method name was "empty()"
	*
	* @public
	* @returns boolean
	*/
	function isEmpty() {

		if( count($this->messages) == 0)
			return True;
		else
			return False;

	}


	/**
	* Return the set of all recorded messages, without distinction
	* by which property the messages are associated with.  If there are
	* no messages recorded, an empty enumeration is returned.
	*
	* <p>To-do
	*/
	function _get() {

		// To-do

    }


	/**
	* Return the set of messages related to a specific property.
	* If there are no such messages, an empty enumeration is returned.
	*
	* @param string The property name (or ActionMessages.GLOBAL_MESSAGE)
	*
	* @public
	* @returns array
	*/
	function get($property) {

		if( array_key_exists($property, $this->messages) ) {
			$ami =  $this->messages[$property]; // ActionMessageItem
		} else {
			$ami = NULL;
		}

		if($ami == NULL)
			return 'EMPTY_LIST';
		else
			return $ami->getList();
	}


	/**
	* Return an individual message string related to a specific property.
	* If there is no such message, a NULL string is returned.
	*
	* @param string	The property name (or ActionMessages.GLOBAL_MESSAGE)
	* @param int		The index position of this message item. A message property
	*                 can have one or more related message items.
	*
	* @public
	* @returns string
	*/
	function getItemString($property, $index=0) {

		// Check it there are message items keyed to this property. Eg: 'logon.password.reqd'
		if( array_key_exists($property, $this->messages) ) {
			$ami =  $this->messages[$property]; // ActionMessageItem
		} else {
			$ami = NULL;
		}

		// Get the set of messages keyed to this property. If no messages for this property
		// then return a NULL string
		if($ami == NULL) {
			return '';
		} else {
			$messageSet = $ami->getList();
		}
		
		// Return the individual message string if one exists, otherwise return a NULL string
		if( array_key_exists($index, $messageSet) ) {
			return $messageSet[$index]->getKey();	// ActionMessage->getKey()
		} else {
			return '';
		}
	}


	/**
	* Return the set of property names for which at least one message has
	* been recorded.  If there are no messages, an empty Iterator is returned.
	* If you have recorded global messages, the String value of
	* <code>ActionMessages.GLOBAL_MESSAGE</code> will be one of the returned
	* property names.
	*
	* @public
	* @returns array
	*/
	function properties() {

        return array_keys($this->messages);

    }


	/**
	* Return the number of messages recorded for all properties (including
	* global messages).  <strong>NOTE</strong> - it is more efficient to call
	* <code>empty()</code> if all you care about is whether or not there are
	* any messages at all.
	*
	* @public
	* @returns int
	*/
	#function size() {
	#
	#	$total = 0;
	#
	#	foreach($this->messages as $key => $value) {
	#		$ami = $value; // ActionMessageItem
	#		$total += count($ami->getList());
	#	}
	#
	#	return $total;
	#
	#}


	/**
	* Return the number of messages associated with the specified property.
	*
	* @param string The property name (or ActionMessages.GLOBAL_MESSAGE)
	* @public
	* @returns int
	*/
	function size($property='') {

		if($property == '') {

			$total = 0;
	
			foreach($this->messages as $key => $value) {
				$ami = $value; // ActionMessageItem
				$total += count($ami->getList());
			}
	
			return $total;

		} else {

			$ami = $this->messages[$property]; // ActionMessageItem
	
			if($ami == NULL)
				return 0;
			else
				return count($ami->getList());
		}

	}

} // end class ActionMessages


/**
* This class is used to store a set of messages associated with a
* property/key and the position it was initially added to list.
*
* <p>Protected
*
* @private
*/
class ActionMessageItem {

	/**
	* The list of <code>ActionMessage</code>s.
	* @private
	* @type array
	*/
	var $list = NULL;

	/**
	* The position in the list of messages.
	* @private
	* @type int
	*/
	var $iOrder = 0;

	function ActionMessageItem($list, $iOrder) {
		$this->list = $list;
		$this->iOrder = $iOrder;
	}

	function &getList() {	// return a reference to list !!
		return $this->list;
	}

	function setList($list) {
		$this->list = $list;
	}

	function getOrder() {
		return $this->iOrder;
	}

	function setOrder($iOrder) {
		$this->iOrder = $iOrder;
	}

}
?>