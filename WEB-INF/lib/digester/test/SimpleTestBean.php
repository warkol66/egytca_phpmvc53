<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/lib/digester/test/SimpleTestBean.php,v 1.3 2006/02/22 08:50:07 who Exp $
* $Revision: 1.3 $
* $Date: 2006/02/22 08:50:07 $
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
* <p> Simple bean used for testing.
*
* @author John C.Wildenauer<br>
*  Credits: Craig McClanahan: Apache Software Foundation (http://www.apache.org/)
* @version $Revision: 1.3 $
* @public 
*/
class SimpleTestBean {

	// ----- Instance Variables --------------------------------------------- //

	var $alpha	= '';		// String
	
	var $beta	= '';		// String
	
	var $gamma	= '';		// String


// ----- Properties ----------------------------------------------------- //

	/**
	* The custom configuration properties.
	* @public
	* @type Array
	*/
	var $properties = array();

	function getProperty($name) {
		
		if( array_key_exists($name, $this->properties) ) {
			return $this->properties[$name];
		}
	}

	/**
	* Add a new custom configuration property.
	*
	* @param  string	Custom property name
	* @param  string	Custom property value
	* @public
	* @returns void
	*/
	function addProperty($name, $value='') {
		$this->properties[$name] = $value;	// put(name, value)
	}


// ----- Public Methods ------------------------------------------------- //

	function getAlpha() {
		return $this->alpha;		// String
	}

	function setAlpha($alpha) {
		$this->alpha = $alpha;	// String
	}

	function getBeta() {
		return $this->beta;		// String
	}

	function setBeta($beta) {
		$this->beta = $beta;		// String
	}

	function getGamma() {
		return $this->gamma;		// String
	}

	function setGamma($gamma) {
		$this->gamma = $gamma;	// String
	}


	function toString() {

		$sb = '[SimpleTestBean]';
		$sb .= ' alpha=';
		$sb .= $this->alpha;
		$sb .= ' beta=';
		$sb .= $this->beta;
		$sb .= ' gamma=';
		$sb .= $this->gamma;

		return $sb;
	}
}
?>