<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/classes/phpmvc/realm/Principal.php,v 1.1 2006/05/17 08:02:13 who Exp $
* $Revision: 1.1 $
* $Date: 2006/05/17 08:02:13 $
*
* ====================================================================
*
* License:	GNU General Public License
*
* Copyright (c) 2006 John C.Wildenauer.  All rights reserved.
* Note: Original work copyright to respective authors
*
* This file is part of php.MVC.
*
* php.MVC is free software; you can redistribute it and/or
* modify it under the terms of the GNU General Public License
* as published by the Free Software Foundation; either version 2
* of the License, or (at your option) any later version. 
* 
* php.MVC is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details. 
* 
* You should have received a copy of the GNU General Public License
* along with this program; if not, write to the Free Software
* Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, 
* USA.
*/

/**
* Principal.
*
* This is an abstract class that can be used to represent any entity (person, 
* corporation, ...) that may require authorisation for access an application.<br>
*
* Ref: java.security Interface Principal.<br>
*
* @author John C Wildenauer
* @version $Revision: 1.1 $
* @public
*/
class Principal {

	// ----- Instance Variables --------------------------------------------- //


	// ----- Constructor ---------------------------------------------------- //

	/**
	* Construct a new Principal.
	*/
	function Principal() {}

	// ----- Protected Methods ---------------------------------------------- //

	/**
	* Compares this <code>Principal</code> to the specified object.
	*
	* Returns True if the object argument matches the <code>Principal</code> 
	* represented by the implementation of this abstract class. Returns false 
	* otherwise.
	
	* @param obj Principal to be compated with.
	* @returns boolean
	*/
	function equals($obj) {}

	/**
	* Returns the name of this <code>Principal</code>.
	*
	* @returns string
	*/
	function getName() {}

	/**
	* Returns a hash code value representing this <code>Principal</code>.
	*
	* Two equal <code>Principal</code> objects will have the same hash code.
	*
	* @returns int
	*/
	function hashCode() {}

	/**
	* Returns a string representation of this principal.
	*
	* @returns string
	*/
	function toString() {}
}
?>