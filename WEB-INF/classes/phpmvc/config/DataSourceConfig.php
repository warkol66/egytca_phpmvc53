<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/classes/phpmvc/config/DataSourceConfig.php,v 1.5 2006/02/22 07:23:12 who Exp $
* $Revision: 1.5 $
* $Date: 2006/02/22 07:23:12 $
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
* <p>A PHP class representing the configuration information of a
* <code>&lt;data-source&gt;</code> element from a php.MVC application
* configuration file.</p>
*
* @author John C. Wildenauer (php.MVC port)<br>
*  Credits:<br>
*  Craig R. McClanahan (original Struts class: see jakarta.apache.org)
* @version $Revision: 1.5 $
*
* @public
*/
class DataSourceConfig {

	// ----- Protected Instance Variables ----------------------------------- //

	/**
	* Has this component been completely configured?
	* @private
	* @type boolean
	*/
	var $configured = False;


	// ----- Properties ----------------------------------------------------- //

	/**
	* The AppServer context attribute key under which this data source
	* is stored and made available.
	* @private
	* @type string
	*/
	var $key = NULL;	// 'DATA_SOURCE'

	/**
	* The custom configuration properties for this data source implementation.
	* @private
	* @type array
	*/
	var $properties = array();

	/**
	* The class name of the <code>xxx.sql.DataSource</code> implementation class.
	* @private
	* @type string
	*/
	var $type = 'BasicDataSource';


	/**
	* Return the AppServer context attribute key.
	* <p>If a custom property key is set, use this as the key for this
	* datasource.
	*
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
			return 'Configuration is frozen';
		}
		$this->key = $key;
	}


	/**
	* @public
	* @returns array
	*/
	function getProperties() {
		return $this->properties;
	}


	/**
	* Return the class name of the <code>DataSource</code> implementation class.
	* <p>If a custom property type is set, use this as the type for this 
	* datasource.
	*
	* @public
	* @returns string
	*/
	function getType() {
		return $this->type;
	}

	/**
	* @param string
	* @public
	* @returns void
	*/
	function setType($type) {
		if($this->configured) {
			return 'Configuration is frozen';
		}
		$this->type = $type;
	}


	// ----- Constructor ---------------------------------------------------- //

	function DataSourceConfig() {
		
		$this->key = Action::getKey('DATA_SOURCE_KEY');

	}


	// ----- Public Methods ------------------------------------------------- //

	/**
	* Return a custom configuration property.
	*
	* @param string	The custom property name
	* @public
	* @returns string
	*/
	function getProperty($name) {

		if( array_key_exists($name, $this->properties) ) {
			return $this->properties[$name];
		} else {
			return NULL;
		}

	}

	/**
	* Add a new custom configuration property.
	*
	* <p>First we look for a setter method matching the $name parameter.
	* For example "host" would become setHost($value). These setter methods
	* would normally be the data-source attributes. Something like:<br>
	* <code>
	* &lt;data-source key="PEAR_MYSQL_DATA_SETUP" type="PearMysqlDataSource"&gt;
	* </code>
	*
	* <p>If a setter method is not found, set the property/value as a custom 
	* property on properties array. These properties would be defined
	* in the set-property elements of the particular data-source element.
	* Something like:<br>
	* &lt;set-property property="host" value="localhost"/&gt;
	*
	* <p><b>Tech note:</b> This behavour varies from the way properties are set
	* on other configuration classes. In those cases we set all properties via
	* the object setter methods. For the DataSourceConfig object we add the
	* custom properties to the <code>$this->properties</code> as shown above.
	* The properties array is used in 
	* <code>ActionServer->initApplicationDataSources(...)</code> to populate
	* the DataSourceDriver instance, e.g. <code>type="PearMysqlDataSource"
	* </code>.
	*
	* @param string	The custom property name
	* @param string	The custom property value
	* @public
	* @returns void
	*/
	function addProperty($name, $value) {

		if($this->configured) {
			return 'Configuration is frozen';
		}


		// XML boolean needs to be converted
		if( strtolower($value) == 'true') {
			$value = True;
		} elseif(strtolower($value) == 'false') {
			$value = False;
		}

		// Look for a setter method matching the "name" parameter
		$beanUtils = new PhpBeanUtils();
		// Try to set a configuration class property first
		// $beanUtils->setProperty(...) returns 1 if a property is found, otherwise 0
		if( $beanUtils->setProperty($this, $name, $value) == 0 ) {

			// If a setter method is not found, set the property/value as a custom 
			// property on properties array.
			// E.g. <set-property property="host" value="localhost"/>
			$this->properties[$name] = $value;	// put(name, value)

		}

	}


	/**
	* Freeze the configuration of this data source.
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

		$strBuff  = 'DataSourceConfig[';
		$strBuff .= 'key="';
		$strBuff .= $this->key;
		$strBuff .= ',type=';
		$strBuff .= $this->type;
		foreach($this->properties as $name => $value) {	
			$strBuff .= ',';
			$strBuff .= $name;
			$strBuff .= '=';
			$strBuff .= $value;
		}
		$strBuff .= ']';
		return $strBuff;

	}


	// ----- Class Serialisation ID ----------------------------------------- //

	/** JCW
	* Serialize version info. This is to ensure that the serialized
	* php.MVC configuration data stored on disk is compatable with
	* this config class.
	* Update this info if making changes to the config classes that
	* would be incompatable with older versions
	*
	* <p>Returns a serial string, something like:
	*    "$className:$fileName:$versionID"
	*
	* @public
	* @returns string
	*/
	function getClassID() {

		// Class ID serialize version info
		$className = 'DataSourceConfig';
		$fileName  = 'DataSourceConfig.php';
		$versionID = '20021025-0955'; // date stamp

		return "$className:$fileName:$versionID";

	}

}
?>