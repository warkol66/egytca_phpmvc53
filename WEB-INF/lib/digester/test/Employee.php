<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/lib/digester/test/Employee.php,v 1.3 2006/02/22 07:26:29 who Exp $
* $Revision: 1.3 $
* $Date: 2006/02/22 07:26:29 $
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
class Employee {

	function Employee($firstName="My First Name", $lastName="My Last Name") {

		$this->setFirstName($firstName);
		$this->setLastName($lastName);
	}

	var $addresses = array(); // ArrayList

	function addAddress($address) {
        $this->addresses[] = $address;	// Address object
    }

	function &getAddress($type) {	// String Eg: $employee->getAddress("office")

		foreach($this->addresses as $k => $oAddr) {
			if( $type == $oAddr->getType() )	// ["home"|"office"|..]
				return $oAddr;
			}
			return NULL;
		}

		// !!!!!!!!!
		function removeAddress($address) {
			$this->addresses[$address] = NULL; // Address
		}

		var $firstName = null; // private String

		function getFirstName() {
			return $this->firstName;
		}

		function setFirstName($firstName) {
			$this->firstName = $firstName; // String
		}

		var $lastName = null; // private String

		function getLastName() {
			return $this->lastName;
		}

		function setLastName($lastName) {
			$this->lastName = $lastName; // String
		}

		// this is to allow testing of primitive convertion 
		var $age;		// private int
		var $active;	// private boolean
		var $salary;	// private float
        
		function getAge() {
			return $this->age;
		}
    
		function setAge($age) {
			$this->age = $age;
		}
    
		function isActive() {
			return $this->active;
		}
    
		function setActive($active) {
			$this->active = $active;
		}
    
		function getSalary() {
			return $this->salary;
		}
    
		function setSalary($salary) {
			$this->salary = $salary;
		}

	function toString() {
		$sb = "Employee[";
		$sb .= "firstName=";
		$sb .= $this->firstName;
		$sb .= ", lastName=";
		$sb .= $this->lastName;
		$sb .= "]";
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
			
			case 'firstName':
				$this->setFirstName($value);
				break;
			case 'lastName':
				$this->setLastName($value);
				break;
			case 'age':
				$this->setAge($value);
				break;
			case 'active':
				$this->setActive($value);
				break;
			case 'salary':
				$this->setSalary($value);
				break;

		}
	}
	
}
?>