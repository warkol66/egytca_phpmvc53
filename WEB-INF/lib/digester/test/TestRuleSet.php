<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/lib/digester/test/TestRuleSet.php,v 1.3 2006/02/22 08:57:05 who Exp $
* $Revision: 1.3 $
* $Date: 2006/02/22 08:57:05 $
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
* RuleSet that mimics the rules set used for Employee and Address creation,
* optionally associated with a particular namespace URI.
*
* @author John C. Wildenauer (php port)<br>
*  Credits: Craig R. McClanahan (Jakata Struts original)
* @version $Revision: 1.3 $
*/
class TestRuleSet extends RuleSetBase {

	// ----- Constructors --------------------------------------------------- //

	/**
	* Construct an instance of this RuleSet associated with the specified
	* prefix and namespace URI.
	*
	* @param string	Matching pattern prefix (must end with '/') or null.
	* @param string	The namespace URI these rules belong to
	*/
	function TestRuleSet($prefix=NULL, $namespaceURI=NULL) {

		if($prefix == NULL)
			$this->prefix = '';
		else
			$this->prefix = $prefix;

		$this->namespaceURI = $namespaceURI;

	}


	// ----- Instance Variables --------------------------------------------- //

	/**
	* The prefix for each matching pattern added to the Digester instance,
	* or an empty String for no prefix.
	*/
	var $prefix = NULL;	// protected String 


	// ----- Public Methods ------------------------------------------------- //

	/**
	* Add the set of Rule instances defined in this RuleSet to the
	* specified <code>Digester</code> instance, associating them with
	* our namespace URI (if any).  This method should only be called
	* by a Digester instance.
	*
	* @param Digester		The Digester instance to which the new Rule instances
	*  should be added.
	*/
	function addRuleInstances(&$digester) {

		$digester->addObjectCreate($this->prefix . 'employee', 'Employee');
		$digester->addSetProperties($this->prefix . 'employee');
		$digester->addObjectCreate('employee/address', 'Address');
		$digester->addSetProperties($this->prefix . 'employee/address');
		$digester->addSetNext($this->prefix . 'employee/address', 'addAddress');
		
	}
    
}
?>