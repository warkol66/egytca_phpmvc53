<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/lib/digester/test/TestRule.php,v 1.3 2006/02/22 08:56:30 who Exp $
* $Revision: 1.3 $
* $Date: 2006/02/22 08:56:30 $
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
* <p>This rule implementation is intended to help test digester.
* The idea is that you can test which rule matches by looking
* at the identifier.</p>
*
* @author John C.Wildenauer<br>
*  Credits: Robert Burrell Donkin: Apache Software Foundation (www.apache.org)
* @version $Revision: 1.3 $
*/
class TestRule extends Rule {

	// ----- Instance Variables --------------------------------------------- //

	/** String identifing this particular <code>TestRule</code> */
	var $identifier;		// String

	/** Used when testing body text */
	var $bodyText = '';	// String

	/** Used when testing call orders */
	var $order = NULL;	// Array (List)


	// ----- Constructors --------------------------------------------------- //

	/**
	* Constructor [with optional namespace URI].
	*
	* @param string		Used to tell which TestRule is which
	* @param string		Set rule namespace
	*/
	function TestRule($identifier, $namespaceURI=NULL) {

		$this->identifier = $identifier;
		$this->setNamespaceURI($namespaceURI);	// base class

	}


	// ----- Rule Methods --------------------------------------------------- //

	/**
	* 'Begin' call.
	*
	* @returns void
	*/
	function begin($attributes, &$digester) {
		$this->appendCall();
	}


	/**
	* Element body call.
	* Save this element body text in this rules $bodyText variable
	*
	* @returns void
	*/
	function body($text, &$digester) {

		$this->digester =& $digester;

		// This is a hack to set the body text for the rules
		// as keeping track of the rules by reference does not
		// work in PHP as it appears to in Java

		// Find the matching rule in the rule(s) stack
		for($i = 0; $i < count($this->digester->rulesMan->rules); $i++) {
			if($this->digester->rulesMan->rules[$i]->identifier == $this->identifier)
				$this->digester->rulesMan->rules[$i]->bodyText = $text;
		}

		$this->appendCall();
	}


	/**
	* 'End' call.
	*
	* @returns void
	*/
	function end(&$digester) {

		$this->appendCall();

	}


// ----- Protected Methods ------------------------------------------------- //

	/**
	* If a list has been set, append this to the list.
	*
	* @returns void
	*/
	function appendCall() {

		$this->order[] = $this;

	}


// ----- Public Methods ---------------------------------------------------- //

	/**
	* Get the body text that was set.
	*
	* @returns String
	*/
	function getBodyText() {

		return $this->bodyText;

	}


	/**
	* Get the identifier associated with this test.
	*
	* @returns String
	*/
	function getIdentifier() {

		return $this->identifier;

	}


	/**
	* Get call order list.
	*
	* @returns Array (List)
	*/
	function getOrder() {

		return $this->order;

	}


	/**
	* Set call order list
	*
	* @param Array	The Rules stack callback reference !!!
	* @returns void
	*/
	function setOrder(&$order) {

		$this->order =& $order;	// Array (List)

	}


	/**
	* Return the identifier.
	*
	* @returns String
	*/
	function toString() {

        return $this->identifier;

	}

}
?>