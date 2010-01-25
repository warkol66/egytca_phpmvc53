<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/lib/digester/RuleSetBase.php,v 1.3 2006/02/22 08:47:00 who Exp $
* $Revision: 1.3 $
* $Date: 2006/02/22 08:47:00 $
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
 * <p>Abbstract convenience base class that implements the {@link RuleSet}
 * interface. Concrete implementations should list all of their actual rule
 * creation logic in the <code>addRuleSet()</code> implementation.</p>
 *
 * @author John C. Wildenauer (php.MVC port)<br>
*  Credits:<br>
 * Craig R. McClanahan (original Struts class)
 * @version $Revision: 1.3 $
 * @public
 */
class RuleSetBase extends RuleSet {

	// ----- Instance Variables --------------------------------------------- //

	/**
	* The namespace URI that all Rule instances created by this RuleSet
	* will be associated with.
	*
	* @type string
	* @private
	*/
	var $namespaceURI = NULL;


	// ----- Properties --------------------------------------------- //

	/**
	* Return the namespace URI that will be applied to all Rule instances
	* created from this RuleSet.
	*
	* @returns string
	* @public
	*/
	function getNamespaceURI() {

		return $this->namespaceURI;

	}


	// ----- Public Methods --------------------------------------------- //

	/**
	* Abstract. Add the set of Rule instances defined in this RuleSet to the
	* specified <code>Digester</code> instance, associating them with
	* our namespace URI (if any).  This method should only be called
	* by a Digester instance.
	*
	* @param Digester The Digester instance to which the new Rule instances
	* should be added.
	* @public
	* @returns void
	*/
	function addRuleInstances($digester) {

		;

	}
}
?>