<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/classes/phpmvc/appserver/AppServerContext.php,v 1.4 2006/05/17 07:16:15 who Exp $
* $Revision: 1.4 $
* $Date: 2006/05/17 07:16:15 $
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
* Class AppServerContext
* Defines a set of methods that a php.MVC application uses to store 
* glogal application attributes and make these attributes available 
* the client php.MVC classes. For example, to get the MIME type of a 
* file ... !!!
*
* @author John C. Wildenauer
* @version $Revision: 1.4 $
* @public
*/
class AppServerContext {

	// ----- Instance Variables --------------------------------------------- //

	/**
	* The Application Server attributes
	* @type array
	*/
	var $appAttributes	= array();	// !!

	/**
	* The Application Server parameters
	* @type array
	*/
	var $parameter			= NULL;		// Setup application paramerers as reqd !!

	/**
	* The Application Server Realm instance to use to authenticate a user Principal
	* @type Principal
	*/
	var $oRealm				= Null;

	/**
	* The LoginConfig instance contains the login properties necessary to allow
	* a user to login and be authorised by the system.
	* @type LoginConfig
	*/
	var $oLoginConfig		= Null;



	// ----- Public Properties ---------------------------------------------- //

	/**
	* Return the Realm instance to use to authenticate a user Principal
	*/
	function getRealm() {
		return $this->oRealm;
	}

	/**
	* Set the Realm instance to use to authenticate a user Principal
	*/
	function setRealm($oRealm) {
		$this->oRealm = $oRealm;
	}

	/**
	* Return the LoginConfig instance to use to authenticate a user Principal
	*/
	function getLoginConfig() {
		return $this->oLoginConfig;
	}

	/**
	* Set the LoginConfig instance to use to authenticate a user Principal
	*/
	function setLoginConfig($oLoginConfig) {
		$this->oLoginConfig = $oLoginConfig;
	}


	// ----- Constructors --------------------------------------------------- //

	function AppServerContext() {

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
	* <p>Returns the specified php.MVC appServerContext attribute</p>
	*
	* @param string	The attribute name
	* @public
	* @returns object
	*/
	function getAttribute($name) {
		if( array_key_exists($name, $this->appAttributes) )
			return $this->appAttributes[$name];
		else
			return NULL;
	}


	/**
	* <p>Sets the specified php.MVC appServerContext attribute</p>
	*
	* @param string	The attribute name
	* @param object - the object to bind to the specified attribute
	* @public
	* @returns
	*/
	function setAttribute($name, &$object) {

		return $this->appAttributes[$name] = $object;

	}

}
?>