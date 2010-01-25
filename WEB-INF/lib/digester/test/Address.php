<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/lib/digester/test/Address.php,v 1.3 2006/02/22 06:59:38 who Exp $
* $Revision: 1.3 $
* $Date: 2006/02/22 06:59:38 $
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
* Bean for Digester testing.
*/
class Address {

	function Address($street="Main Street", $city="Mossman", 
							$state="Down Under", $zipCode="MyZip") {
		$this->setStreet($street);		// String
		$this->setCity($city);			// String
		$this->setState($state);			// String
		$this->setZipCode($zipCode);	// String
	}

	var $city = NULL; // private String

	function getCity() {
		return $this->city; // String
	}

	function setCity($city) {
		$this->city = $city; // String
	}


	var $state = NULL; // private String

	function getState() {
		return $this->state; // String
	}

	function setState($state) {
		$this->state = $state; // String
	}


	var $street = NULL; // private String

	function getStreet() {
		return $this->street; // String
	}

	function setStreet($street) {
		$this->street = $street; // String
	}


	var $type = NULL; // private String

	function getType() {
		return $this->type; // String
	}

	function setType($type) {
		$this->type = $type; // String
	}


	var $zipCode = NULL; // private String

	function getZipCode() {
		return $this->zipCode; // String
	}

	function setZipCode($zipCode) {
		$this->zipCode = $zipCode; // String
	}


	function setEmployee(&$employee) {
		$employee->addAddress($this);	// Employee
	}


	function toString() {
		$sb = "Address[";
		$sb = "street=";
		$sb = $this->street;
		$sb = ", city=";
		$sb = $this->city;
		$sb = ", state=";
		$sb = $this->state;
		$sb = ", zipCode=";
		$sb = $this->zipCode;
		$sb = "]";
		return $sb;
    }


	/* JCW
	* Add a new custom configuration property.
	*
	* @param name String, Custom property name
	* @param value String, Custom property value
	* @access public
	* @return void
	*/
	function addProperty($name, $value) {

		##### FIX THESE SETTERS #####
		switch($name) {
			
			case 'city':
				$this->setCity($value);
				break;
			case 'state':
				$this->setState($value);
				break;
			case 'street':
				$this->setStreet($value);
				break;
			case 'type':
				$this->setType($value);
				break;
			case 'zipCode':
				$this->setZipCode($value);
				break;
		}

	}

}
?>