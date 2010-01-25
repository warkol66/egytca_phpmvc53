<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/classes/phpmvc/test/PlugInDriverTestClass.php,v 1.2 2006/02/22 08:31:28 who Exp $
* $Revision: 1.2 $
* $Date: 2006/02/22 08:31:28 $
*
* ====================================================================
*
* License:	GNU Lesser General Public License (LGPL)
*
* Copyright (c) 2004-2006 John C.Wildenauer.  All rights reserved.
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

// APlugIn is an abstract class that provides a means of dynamically loading
// and accessing additional modules within php.MVC in a pluggable manner.
// See: \WEB-INF\classes\phpmvc\plugins\APlugIn.php
include_once 'APlugIn.php';




/**
* PlugIn test driver class A.
*
* @author John C Wildenauer
* @version $Revision: 1.2 $
*/

class PlugInTestDriverA extends APlugIn {

	// Constructor
	function PlugInTestDriverA() {
		// Build the parent first
		parent::APlugIn();

		// And save a reference to the class instance
		$this->plugIn =& new PlugInTestClassA;	// Normally just a class like "Smarty"
	}
}

/**
* PlugIn test class A. 
* This class represents a third party class we want to use, like "Smarty".
*
* @author John C Wildenauer
* @version $Revision: 1.2 $
*/

class PlugInTestClassA {

	// Properties
	var $propA1 = '';
	var $propA2 = '';

	function setPropA1($property) {
		$this->propA1 = $property;
	}
	function getPropA1() {
		return $this->propA1;
	}

	function setPropA2($property) {
		$this->propA2 = $property;
	}
	function getPropA2() {
		return $this->propA2;
	}

	// Constructor
	function PlugInTestClassA() {
		;
	}
}



class PlugInTestDriverB extends APlugIn {

	// Properties

	// Constructor
	function PlugInTestDriverB() {
		// Build the parent first
		parent::APlugIn();

		// And save a reference to the class instance
		$this->plugIn =& new PlugInTestClassB;	// Normally just a class like "Smarty"
	}
}


/**
* PlugIn test class B.
* This class represents a third party class we want to use, like "Smarty".
*
* @author John C Wildenauer
* @version $Revision: 1.2 $
*/
class PlugInTestClassB {

	// Properties 
	var $propB1 = '';
	var $propB2 = '';

	function setPropB1($property) {
		$this->propB1 = $property;
	}
	function getPropB1() {
		return $this->propB1;
	}

	function setPropB2($property) {
		$this->propB2 = $property;
	}
	function getPropB2() {
		return $this->propB2;
	}

	// Constructor
	function PlugInTestClassB() {
		;
	}	
}

?>