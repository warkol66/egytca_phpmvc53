<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/classes/phpmvc/config/FormBeanConfig.php,v 1.5 2006/02/22 07:29:18 who Exp $
* $Revision: 1.5 $
* $Date: 2006/02/22 07:29:18 $
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
* <code>&lt;form-bean&gt;</code> element in a Struts application
* configuration file.<p>
*
* @author John C. Wildenauer (php.MVC port)<br>
*  Credits:<br>
*  Craig R. McClanahan (Jakata Struts class: see jakarta.apache.org)
* @version $Revision: 1.5 $
* @public
*/
class FormBeanConfig {

	// ----- Protected Instance Variables ----------------------------------- //

	/**
	* Has this component been completely configured?
	* @private
	* @type boolean
	*/
	var $configured = False;


	/**
	* The set of FormProperty elements defining dynamic form properties for
	* this form bean, keyed by property name.
	*
	* @private
	* @type array
	*/
	var $formProperties = array();


	// ----- Properties ----------------------------------------------------- //

	/**
	* Is the form bean class an instance of DynaActionForm with dynamic
	* properties?
	* @private
	* @type boolean
	*/
	var $dynamic = False;

	/**
	* The unique identifier of this form bean, which is used to reference this
	* bean in <code>ActionMapping</code> instances as well as for the name of
	* the request or session attribute under which the corresponding form bean
	* instance is created or accessed.
	*
	* @private
	* @type string
	*/
	var $name = NULL;

	/**
	* The fully qualified Java class name of the implementation class
	* to be used or generated.
	* @private
	* @type string
	*/
	var $type = NULL;

	/** JCW<br>
	* The custom configuration properties for this data source implementation.
	* @private
	* @type array
	*/
	#var $properties = array();


	/**
	* @public
	* @returns boolean
	*/
	function getDynamic() {
		return $this->dynamic;
	}

	/**
	* @param boolean
	* @public
	* @returns void
	*/
	function setDynamic($dynamic) {
		if($this->configured) {
			return "Configuration is frozen";
		}
		$this->dynamic = $dynamic;
	}


	/**
	* @public
	* @returns string
	*/
	function getName() {
		return $this->name;
	}

	/**
	* @param string
	* @public
	* @returns void
	*/
	function setName($name) {
		if($this->configured) {
			return "Configuration is frozen";
		}
		$this->name = $name;
	}


	/**
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
			return "Configuration is frozen";
		}
		$this->type = $type;
		if('phpmvc.action.DynaActionForm' == $type) {
			$this->dynamic = True;
		}
	}


	/** JCW<br>
	* Add a new custom configuration property.
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
		$beanUtils->setProperty($this, $name, $value);

	}


	// ----- Public Methods ------------------------------------------------- //

	/**
	* Add a new <code>FormPropertyConfig</code> instance to the set associated
	* with this application.
	*
	* @param FormPropertyConfig	The new configuration instance to be added
	* @public
	* @returns void
	*/
	function addFormPropertyConfig($config) {

		if($this->configured) {
			return "Configuration is frozen";
		}

		$this->formProperties[$config->getName()] = $config;
	}


	/**
	* Return the form property configuration for the specified property
	* name, if any; otherwise return <code>NULL</code>.
	*
	* @param string	The form property name to find a configuration for
	* @public
	* @returns FormPropertyConfig
	*/
	function findFormPropertyConfig($name) {

		return $this->formProperties[$name]; // (FormPropertyConfig)

	}


	/**
	* Return the form property configurations (FormPropertyConfig[]) for this 
	* application. If there are none, a zero-length array is returned.
	* @public
	* @returns array
	*/
	function findFormPropertyConfigs() {
		return array_values($this->formProperties);
	}


	/**
	* Freeze the configuration of this component.
	* @public
	* @returns void
	*/
	function freeze() {

		$this->configured = True;

		$fpconfigs = array();
		$fpconfigs = $this->findFormPropertyConfigs(); // FormPropertyConfig[]

		foreach($fpconfigs as $fpconfig) {
			$fpconfig->freeze();
		}

	}


	/**
	* Remove the specified form property configuration instance.
	*
	* @param FormPropertyConfig	The FormPropertyConfig instance to be removed
	* @public
	* @returns void
	*/
	function removeFormPropertyConfig($config) {

		if($this->configured) {
			return "Configuration is frozen";
		}
		$this->formProperties->remove($config->getName());

	}


	/**
	* Return a String representation of this object
	* @public
	* @returns string
	*/
	function toString() {
	
		$sb = 'FormBeanConfig[';
		$sb .= 'name=';
		$sb .= $this->name;
		$sb .= ',type=';
		$sb .= $this->type;
		$sb .= ']';
		return $sb;

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
		$className = 'FormBeanConfig';
		$fileName  = 'FormBeanConfig.php';
		$versionID = '20021025-0955'; // date stamp

		return "$className:$fileName:$versionID";

	}

}
?>