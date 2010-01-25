<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/classes/phpmvc/test/TestPhpBean.php,v 1.3 2006/02/22 08:55:26 who Exp $
* $Revision: 1.3 $
* $Date: 2006/02/22 08:55:26 $
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
* General purpose test phpBean for phpUnit tests for the "beanutils" component.
*
* @author John C Wildenauer (php.MVC port)<br>
*  Credits: Craig R. McClanahan (original Struts class: see jakarta.apache.org)
* @version $Revision: 1.3 $
*/

class TestPhpBean {


	// ----- Properties ----------------------------------------------------- //


	/**
	* A boolean property.
	*/
	var $booleanProperty = True;

	function getBooleanProperty() {
		return $this->booleanProperty;
	}

	function setBooleanProperty($booleanProperty) {
		$this->booleanProperty = $booleanProperty;
	}


	/**
	* A boolean property that uses an "is" method for the getter.
	*/
	# ...

	/**
	* A double property.
	*/
	# ...


	/**
	* An "indexed property" accessible via both array and subscript
	* based getters and setters.
	*/
	# ...


	/**
	* A float property.
	*/
	# ...


	/**
	* An integer array property accessed as an array.
	*/
	# ...


	/**
	* An integer array property accessed as an indexed property.
	*/
	# ...


	/**
	* An integer property.
	*/
	var $intProperty = 123;

	function getIntProperty() {
		return $this->intProperty;
	}

	function setIntProperty($intProperty) {
		$this->intProperty = intProperty;
	}


	/**
	* A List property accessed as an indexed property.
	*/
	# ...


	/**
	* A long property.
	*/
	# ...


	/**
	* A mapped property with only a getter and setter for a Map.
	*/
	# ...


	/**
	* A mapped property that has String keys and Object values.
	*/
	# ...


	/**
	* A mapped property that has String keys and String values.
	*/
	# ...


	/**
	* A mapped property that has String keys and int values.
	*/
	# ...


	/**
	* A nested reference to another test bean (populated as needed).
	*/
	# ...

	/**
	* A String property with an initial value of null.
	*/
	# ...


	/**
	* A read-only String property.
	*/
	var $readOnlyProperty = "Read Only String Property";

	function getReadOnlyProperty() {
        return $this->readOnlyProperty;
	}


	/**
	* A short property.
	*/
	# ...


	/**
	* A String array property accessed as a String.
	*/
	# ...


	/**
	* A String array property accessed as an indexed property.
	*/
	# ...


	/**
	* A String property.
	*/
	var $stringProperty = "This is a string";

	function getStringProperty() {
		return $this->stringProperty;
	}

	function setStringProperty($stringProperty) {
		$this->stringProperty = $stringProperty;
    }


	/**
	* A write-only String property.
	*/
	var $writeOnlyProperty = "Write Only String Property";

	function getWriteOnlyPropertyValue() {
		return $this->writeOnlyProperty;
	}

	function setWriteOnlyProperty($writeOnlyProperty) {
		$this->writeOnlyProperty = $writeOnlyProperty;
	}


	// ----- Static Variables ----------------------------------------------- //
	## RE-VISIT
	/**
	* A static variable that is accessed and updated via static methods
	* for MethodUtils testing.
	*/
	var $counter = 0;	// static !!!


	/**
	* Return the current value of the counter.
	*/
	function currentCounter() {

		return $this->counter;

	}


	/**
	* Increment the current value of the counter by 1.
	*/
	function incrementCounter() {

        incrementCounter(1);

    }


	/**
	* Increment the current value of the counter by the specified amount.
	*
	* @param integer The Amount to be added to the current counter
	*/
	function incrementCounter($amount) {

		$this->counter += $amount;

	}

}
?>