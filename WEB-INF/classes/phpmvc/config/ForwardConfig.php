<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/classes/phpmvc/config/ForwardConfig.php,v 1.7 2006/02/22 07:30:50 who Exp $
* $Revision: 1.7 $
* $Date: 2006/02/22 07:30:50 $
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
* A Bean representing the configuration information of a
* <code>&lt;forward&gt;</code> element from a php.MVC application
* configuration file.
*
* @author John C. Wildenauer (php.MVC port)<br>
*  Credits:<br>
*  Craig R. McClanahan (Tomcat/catalina class: see jakarta.apache.org)
* @version $Revision: 1.7 $
* @public
*/
class ForwardConfig {

	// ----- Protected Instance Variables ----------------------------------- //

	/**
	* Has this component been completely configured?
	* @private
	* @type boolean
	*/
	var $configured = False;


	// ----- Properties ----------------------------------------------------- //

	/**
	* Should the value of the <code>path</code> property be considered
	* context-relative if it starts with a slash (and therefore not
	* prefixed with the application prefix?
	*
	* @private
	* @type boolean
	*/
	var $contextRelative = False;

	/**
	* The unique identifier of this forward, which is used to reference it
	* in <code>Action</code> classes.
	* @private
	* @type string
	*/
	var $name = NULL;

	/**
	* The module-relative or context-relative path to the static resource that is
	* given by the logical name of this ForwardConfig. This is normally a page 
	* or template. Eg: "stdLogon.php". .
	* @private
	* @type string
	*/
	var $path = NULL;

	/**
	* The path to the Action resources refered to by this ForwardConfig. This is
	* normally an Action class that is in the application environment path. 
	* Eg: "myActionPath2".
	* <p>Either a "path" or an "nextActionPath" (or both) are given for a forward
	* element. If both a "path" and an "nextActionPath" are given, the static
	* resource (Eg: "stdLogon.php") will be called before control is passed to the
	* next Action in the chain.
	* @private
	* @type string
	*/
	var $nextActionPath = NULL;

	/**
	* Should a redirect be used to transfer control to the specified path?
	* @private
	* @type boolean
	*/
	var $redirect = False;

	/** JCW<br>
	* The custom configuration properties for this data source implementation.
	* @private
	* @type array
	*/
	var $properties = array();


	/**
	* @public
	* @returns boolean
	*/
	function getContextRelative() {
		return $this->contextRelative;
	}

	/**
	* @param boolean
	* @public
	* @returns void
	*/ 
	function setContextRelative($contextRelative) {
		if($this->configured) {
			return 'Configuration is frozen';
		}
		$this->contextRelative = $contextRelative;
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
			return 'Configuration is frozen';
		}
		$this->name = $name;
	}

	/**
	* @public
	* @returns string
	*/
	function getPath() {
		return $this->path;
	}

	/**
	* Set the path to the static resource.
	*
	* <p>The path string is decoded with urldecode(). This is necessary to handle
	* request redirect paths that contain "&" separator characters. For example:<br>
	* <code>
	*    &lt;forward 
	*       name     = "redirect_path" 
	*       path     = "http://www.myhost.com/myapp/Main.php?do=myAction&cmd=listItems"
	*       redirect = "true"/&gt;
	* </code>
	* 
	* <p>The XML processor will not accept character data strings containing the "&" 
	* character, and PHP will not correctly handle query strings with the hex 
	* replacement character "%26". So we decode (un-hex) the path string here.
	*
	* @param string
	* @public
	* @returns void
	*/
	function setPath($path) {
		if($this->configured) {
			return 'Configuration is frozen';
		}
		$URLDecodedPath = '';
		$URLDecodedPath = urldecode($path);
		$this->path = $URLDecodedPath;
	}

	/**
	* @public
	* @returns string
	*/
	function getNextActionPath() {
		return $this->nextActionPath;
	}

	/**
	* @param string
	* @public
	* @returns void
	*/
	function setNextActionPath($nextActionPath) {
		if($this->configured) {
			// JCW: 27-10.2004
			// We need to be able to reset the nextActionPath even if this ForwardConfig is frozen
			#return 'Configuration is frozen'
		}
		$this->nextActionPath = $nextActionPath;
	}

	/**
	* @public
	* @returns boolean
	*/
	function getRedirect() {
        return $this->redirect;
    }

	/**
	* @param boolean
	* @public
	* @returns void
	*/
	function setRedirect($redirect) {
		if($this->configured) {
			return 'Configuration is frozen';
		}
		$this->redirect =$redirect;
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


	// ----- Constructor ---------------------------------------------------- //

	/**
	* Construct a new instance with the specified path and redirect flag.
	*
	* @param string	Path for this instance [optional]
	* @param boolean	Redirect flag for this instance [optional]
	*/
	function ForwardConfig($path='', $redirect='') {

		$this->setName(NULL);
		$this->setPath($path);
		$this->setRedirect($redirect);		

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

		$sb = 'ForwardConfig[';
		$sb .= 'name=';
		$sb .= $this->name;
		$sb .= ',path=';
		$sb .= $this->path;
		$sb .= ',redirect=';
		$sb .= $this->redirect;
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
		$className = 'ForwardConfig';
		$fileName  = 'ForwardConfig.php';
		$versionID = '20021025-0955'; // date stamp

		return "$className:$fileName:$versionID";

	}

}
?>