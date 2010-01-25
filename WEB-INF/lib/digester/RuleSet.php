<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/lib/digester/RuleSet.php,v 1.3 2006/02/22 08:46:41 who Exp $
* $Revision: 1.3 $
* $Date: 2006/02/22 08:46:41 $
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
* <p>Public interface defining a shorthand means of configuring a complete
* set of related <code>Rule</code> definitions, possibly associated with
* a particular namespace URI, in one operation.  To use an instance of a
* class that imlements this interface:</p>
* <ul>
* <li>Create a concrete implementation of this interface.</li>
* <li>Optionally, you can configure a <code>RuleSet</code> to be relevant
*     only for a particular namespace URI by configuring the value to be
*     returned by <code>getNamespaceURI()</code>.</li>
* <li>As you are configuring your Digester instance, call
*     <code>digester.addRuleSet()</code> and pass the RuleSet instance.</li>
* <li>Digester will call the <code>addRuleInstances()</code> method of
*     your RuleSet to configure the necessary rules.</li>
* </ul>
*
* @author John C. Wildenauer (php.MVC port)<br>
*  Credits:<br>
*  Craig R. McClanahan (original Jakarta Struts: see jakarta.apache.org)
* @version $Revision: 1.3 $
* @public
*/
class RuleSet {

	// ----- Properties --------------------------------------------- //

	/**
	* Return the namespace URI that will be applied to all Rule instances
	* created from this RuleSet.
	*
	* @returns string	
	* @public
	*/
	function getNamespaceURI() {}


	// ----- Public Methods --------------------------------------------- //

	/**
	* Add the set of Rule instances defined in this RuleSet to the
	* specified <code>Digester</code> instance, associating them with
	* our namespace URI (if any). This method should only be called
	* by a Digester instance.
	*
	* @param object The Digester, Digester instance to which the new Rule
	* instances should be added.
	*
	* @returns void
	* @public
	*/
	function addRuleInstances($digester) {

		;

	}
}
?>