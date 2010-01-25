<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/lib/digester/Rules.php,v 1.3 2006/02/22 08:45:48 who Exp $
* $Revision: 1.3 $
* $Date: 2006/02/22 08:45:48 $
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
* Public abstract (interface) defining a collection of Rule instances (and 
* corresponding matching patterns) plus an implementation of a matching policy
* that selects the rules that match a particular pattern of nested elements 
* discovered during parsing.
*
* @author John C. Wildenauer (php.MVC port)<br>
*  Credits:<br>
*  Craig R. McClanahan (Jakarta Strurs)
* @version $Revision: 1.3 $
*/
class Rules {

	// ----- Properties ----------------------------------------------------- //

	/**
	* Return the Digester instance with which this Rules instance is associated.
	*
	* @returns Digester
	* @public
	*/
	function getDigester() {}


	/**
	* Set the Digester instance with which this Rules instance is associated.
	*
	* @param Digester The newly associated Digester instance
	* @returns void
	* @public
	*/
	function setDigester($digester) {}


	/**
	* Return the namespace URI that will be applied to all subsequently
	* added <code>Rule</code> objects.
	*
	* @returns string
	* @public
	*/
	function getNamespaceURI() {}


	/**
	* Set the namespace URI that will be applied to all subsequently
	* added <code>Rule</code> objects.
	*
	* @param string The namespace URI that must match on all
	*  subsequently added rules, or <code>NULL</code> for matching
	*  regardless of the current namespace URI
	* @returns void
	* @public
	*/
	function setNamespaceURI($namespaceURI) {}


	// ----- Public Methods ------------------------------------------------- //

	/**
	* Register a new Rule instance matching the specified pattern.
	*
	* @param string The nesting pattern to be matched for this Rule
	* @param Rule The Rule instance to be registered
	* @returns void
	* @public
	*/
	function add($pattern, $rule) {}


	/**
	* Clear all existing Rule instance registrations.
	*
	* @returns void
	* @public
	*/
	function clear() {}


	/**
	* Return a List of all registered Rule instances that match the specified
	* nesting pattern, or a zero-length List if there are no matches.  If more
	* than one Rule instance matches, they <strong>must</strong> be returned
	* in the order originally registered through the <code>add()</code>
	* method.
	*
	* @param string The namespace URI for which to select matching rules,
	*  or <code>NULL</code> to match regardless of namespace URI
	* @param string The nesting pattern to be matched
	* @returns array
	* @public
	*/
	function match($namespaceURI, $pattern) {}


	/**
	* Return a List of all registered Rule instances, or a zero-length List
	* if there are no registered Rule instances.  If more than one Rule
	* instance has been registered, they <strong>must</strong> be returned
	* in the order originally registered through the <code>add()</code>
	* method.
	*
	* @returns array
	* @public
	*/
	function rules() {

		;

	}
}
?>