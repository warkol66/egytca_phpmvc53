<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/classes/phpmvc/appserver/AppServerConfig.php,v 1.6 2006/05/21 22:14:36 who Exp $
* $Revision: 1.6 $
* $Date: 2006/05/21 22:14:36 $
*
* ====================================================================
*
* License:	GNU Lesser General Public License (LGPL)
*
* Copyright (c) 2003-2006 John C.Wildenauer.  All rights reserved.
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
* Class AppServerConfig
* <p>Stores startup and global configuration information for an php.MVC Web
* application, and makes this information available to the application 
* instance.
*
* @author John C. Wildenauer
* @version $Revision: 1.6 $
* @public
*/
class AppServerConfig {

	// ----- Instance Variables --------------------------------------------- //

	/**
	* The mapping (ActionConfig)
	* @type string
	*/
	var $mapping = 'ActionConfig';	// default mapping (dep ActionMapping)

	/**
	* The AppServerContext
	* @type AppServerContext
	*/
	var $context = NULL;					// request context

	/**
	* The Application Server parameters
	* @param array
	*/
	var $parameter = array();			// Setup application paramerers as reqd


	// ----- Constructors --------------------------------------------------- //

	function AppServerConfig() {

		;

	}


	// ----- Public Methods ------------------------------------------------- //

	/**
	* <p>Returns a String value of the named initialization parameter, 
	* or null if the parameter is not set.</p>
	*
	* @param string The name of the initialization parameter
	* @public
	* @returns string
	*/
	function getInitParameter($name) {

		if( array_key_exists($name, $this->parameter) )
			return $this->parameter[$name];
		else
			return NULL;

	}


	/**
	* <p>Set the value of the named initialization parameter.</p>
	*
	* @param string	The name of the initialization parameter
	* @param string	The value of the intialization parameter
	* @public
	* @returns void
	*/
	function setInitParameter($key, $value) {

		$this->parameter[$key] = $value;

	}


	/**
	* <p>Returns the php.MVC appServerContext object</p>
	*
	* @public
	* @returns AppServerContext
	*/
	function &getAppServerContext() {

		return $this->context;

	}


	/**
	* <p>Sets the php.MVC appServerContext object</p>
	*
	* @public
	* @returns AppServerContext
	*/
	function setAppServerContext(&$context) {

		$this->context =& $context;

	}

}
?>