<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/lib/digester/Rule.php,v 1.3 2006/02/22 08:45:29 who Exp $
* $Revision: 1.3 $
* $Date: 2006/02/22 08:45:29 $
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
* Concrete implementations of this class implement actions to be taken when
* a corresponding nested pattern of XML elements has been matched.
*
* @author John C. Wildenauer (php.MVC port)<br>
*  Credits:<br>
*  Craig McClanahan (original Jakarta Struts: see jakarta.apache.org)
* @version $Revision: 1.3 $
*/
class Rule {

	// ----- Instance Variables --------------------------------------------- //

	/**
	* The Digester with which this Rule is associated.
	*
	* Note: Saving a reference to the digester instance here does not work !!
	*			Pass &digester reference to the digester instance in the method 
	*			calls works. See begin() and end()
	*
	* @type Digester
	* @private
	*/
	var $digester = NULL;		// Digester object reference

	/**
	* The namespace URI for which this Rule is relevant, if any.
	*
	* @type string
	* @private
	*/
	var $namespaceURI = NULL;	// String


	// ----- Constructors --------------------------------------------------- //
  
	/**
	* Base constructor.
	* <p>The digester will be set when the rule is added.</p>
	*/
    function Rule() {}


	// ----- Properties ----------------------------------------------------- //

	/**
	* Return the Digester with which this Rule is associated.
	*
	* @public
	* @returns Digester
	*/
	function getDigester() {

		return $this->digester;

	}

	/**
	* Set the <code>Digester</code> with which this <code>Rule</code>
	*  is associated.
	*
	* @param Digester A Digester object reference
	* @public
	* @returns void
	*/
	function setDigester(&$digester) {
        
		$this->digester =& $digester;	// note the two rererences '&'
        
	}

	/**
	* Return the namespace URI for which this Rule is relevant, if any.
	*
	* @public
	* @returns string
	*/
	function getNamespaceURI() {

		return $this->namespaceURI;

	}


	/**
	* Set the namespace URI for which this Rule is relevant, if any.
	*
	* @param string The namespace URI for which this Rule is relevant,
	*  or <code>NULL</code> to match independent of namespace.
	* @public
	* @returns void
	*/
	function setNamespaceURI($namespaceURI) {

		$this->namespaceURI = $namespaceURI;

	}


	// ----- Public Methods ------------------------------------------------- //

	/**
	* This method is called when the beginning of a matching XML element
	* is encountered.
	*
	* @param array The attribute list of this element
	* @param Digester A reference to the digester instance
	* @returns void
	* @public
	*/
	function begin($attributes, &$digester) {

		// Implement this in the inheriting method
		$this->digester =& $digester;

		;	// The default implementation does nothing

	}


	/**
	* This method is called when the body of a matching XML element
	* is encountered.  If the element has no body, this method is
	* not called at all.
	*
	* @param string The text of the body of this element
	* @param Digester A reference to the digester instance
	* @returns void
	* @public
	*/
	function body($text, &$digester) {

		;	// The default implementation does nothing

	}


	/**
	* This method is called when the end of a matching XML element
	* is encountered.
	*
	* @param Digester A reference to the digester instance
	* @returns void
	* @public
	*/
	function end(&$digester) {

		// Implement this in the inheriting method
		$this->digester =& $digester;

		;	// The default implementation does nothing

	}


	/**
	* This method is called after all parsing methods have been
	* called, to allow Rules to remove temporary data.
	*
	* @returns void
	* @public
	*/
	function finish() {

		;	// The default implementation does nothing

	}
}
?>