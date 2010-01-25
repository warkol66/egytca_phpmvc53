<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/classes/phpmvc/config/MessageResourcesConfig.php,v 1.3 2006/02/22 08:18:42 who Exp $
* $Revision: 1.3 $
* $Date: 2006/02/22 08:18:42 $
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
* <p>A Bean representing the configuration information of a
* <code>&lt;message-resources&gt;</code> element in a php.MVC application
* configuration file.</p>
*
* @author John C. Wildenauer (php.MVC port)<br>
*  Credits:<br>
*  Craig R. McClanahan (original Jakata Struts class)
* @version $Revision: 1.3 $
* @public
*/
class MessageResourcesConfig {

	// ----- Protected Instance Variables ----------------------------------- //

	/**
	* Has this component been completely configured?
	* @private
	* @type boolean
	*/
	var $configured = False;


	// ----- Properties ----------------------------------------------------- //

	/**
	* Fully qualified class name of the MessageResourcesFactory class
	* we should use.
	* @private
	* @type string
	*/
	var $factory = 'PropertyMessageResourcesFactory';

	/**
	* The appserver context attributes key under which this MessageResources
	* instance is stored.
	* @private
	* @type string
	*/
	var $key = ''; // Action::getKey('MESSAGES_KEY')

	/**
	* Should we return <code>NULL</code> for unknown message keys?
	* @private
	* @type boolean
	*/
	var $nullValue = True;

	/**
	* Parameter that is passed to the <code>createResources()</code> method
	* of our MessageResourcesFactory implementation.
	* @private
	* @type string
	*/
	var $parameter = NULL;


	/**
	* @public
	* @returns string
	*/
	function getFactory() {
		return $this->factory;
	}

	/**
	* @param string
	* @public
	* @returns void
	*/
	function setFactory($factory) {
		if($this->configured) {
			return 'IllegalStateException(Configuration is frozen)';
		}
		$this->factory = $factory;
	}


	/**
	* @public
	* @returns string
	*/
	function getKey() {
		return $this->key;
	}

	/**
	* @param string
	* @public
	* @returns void
	*/
	function setKey($key) {
		if($this->configured) {
			return 'IllegalStateException(Configuration is frozen)';
		}
		$this->key = $key;
	}


	/**
	* @public
	* @returns boolean
	*/
	function getNull() {
		return $this->nullValue;
	}

	/**
	* @param boolean
	* @public
	* @returns void
	*/
	function setNull($nullValue) {
		if($this->configured) {
			return 'IllegalStateException(Configuration is frozen)';
		}
		$this->nullValue = $nullValue;
	}


	/**
	* @public
	* @returns string
	*/
	function getParameter() {
		return $this->parameter;
	}

	/**
	* @param string
	* @public
	* @returns void
	*/
	function setParameter($parameter) {
		if($this->configured) {
			return 'IllegalStateException(Configuration is frozen)';
		}
		$this->parameter = $parameter;
	}


	// ----- Public Methods ------------------------------------------------- //

	/**
	* Freeze the configuration of this component.
	* @public
	* @returns void
	*/
	function freeze() {

		$this->configured = True;

	}


	/**
	* Return a String representation of this object.
	* @public
	* @returns string
	*/
	function toString() {

		$sb = 'MessageResourcesConfig[';
		$sb .= 'factory=';
		$sb .= $this->factory;
		$sb .= 'null=';
		$sb .= $this->nullValue;
		$sb .= ',parameter=';
		$sb .= $this->parameter;
		$sb .= ']';
		return $sb;

	}
}
?>