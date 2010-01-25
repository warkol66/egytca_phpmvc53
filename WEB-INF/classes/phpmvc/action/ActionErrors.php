<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/classes/phpmvc/action/ActionErrors.php,v 1.4 2006/02/24 06:54:58 who Exp $
* $Revision: 1.4 $
* $Date: 2006/02/24 06:54:58 $
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
* <p>A class that encapsulates the error messages being reported by
* the <code>validate()</code> method of an <code>ActionForm</code>.
* Validation errors are either global to the entire <code>ActionForm</code>
* bean they are associated with, or they are specific to a particular
* bean property (and, therefore, a particular input field on the corresponding
* form).</p>
*
* <p>Each individual error is described by an <code>ActionError</code>
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
*  David Geary (Jakata Struts: see jakarta.apache.org)
*  Craig R. McClanahan (Jakata Struts: see jakarta.apache.org)
* @revision $Revision: 1.4 $
* @public
*/
class ActionErrors extends ActionMessages {

	// ----- Manifest Constants --------------------------------------------- //

	/**
	* The "property name" marker to use for global errors, as opposed to
	* those related to a specific property.
	* @private
	* @type string
	*/
	var $GLOBAL_ERROR = 'phpmvc.action.GLOBAL_ERROR'; // public static final !!

	/** JCW
	* Return the GlobalErrorKey key string
	* @public	
	* @returns string
	*/
	function getGlobalErrorKey() {

		return $this->GLOBAL_ERROR;
	}


	// ----- Public Methods ------------------------------------------------- //

	/**
	* Add an error message to the set of errors for the specified property.
	*
	* @param string		Property name (or ActionErrors.GLOBAL_ERROR)
	* @param ActionError	The error message to be added
	* @public
	* @return void 
	*/
	function add($property, $error) {

		parent::add($property, $error);

	}

}
?>