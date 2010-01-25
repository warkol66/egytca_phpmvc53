<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/classes/phpmvc/realm/LoginConfig.php,v 1.1 2006/05/17 08:02:13 who Exp $
* $Revision: 1.1 $
* $Date: 2006/05/17 08:02:13 $
*
* ====================================================================
*
* License:	GNU General Public License
*
* Copyright (c) 2006 John C.Wildenauer.  All rights reserved.
* Note: Original work copyright to respective authors
*
* This file is part of php.MVC.
*
* php.MVC is free software; you can redistribute it and/or
* modify it under the terms of the GNU General Public License
* as published by the Free Software Foundation; either version 2
* of the License, or (at your option) any later version. 
* 
* php.MVC is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details. 
* 
* You should have received a copy of the GNU General Public License
* along with this program; if not, write to the Free Software
* Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, 
* USA.
*/


/**
* Representation of a login configuration element for a web application,
* as represented in a <code>&lt;login-config&gt;</code> element in the
* deployment descriptor.
*
* Credits: Craig R. McClanahan (Tomcat-Catalina)

* @author John C. Wildenauer
* @version $Revision: 1.1 $
*/
class LoginConfig {

	// ----- Private Properties --------------------------------------------- //

	/**
	* The authentication method to use for application login.  Must be
	* BASIC, DIGEST, FORM, CLIENT-CERT or SERVICE.
	* @type string
	*/
	var $sAuthMethod = 'SERVICE';	

	/**
	* The context-relative URI of the error page for form login.
	* @type string
	*/
	var $sErrorPage = '';	

	/**
	* The context-relative URI of the login page for form login.
	* @type string
	*/
	var $sLoginPage = '';

	/**
	* The realm name used when challenging the user for authentication 
	* credentials.
	* @type string
	*/
	var $sRealmName = '';


	// ----- Properties ----------------------------------------------------- //

	function getAuthMethod() {
		return $this->sAuthMethod;
	}
	function setAuthMethod($sAuthMethod) {
		$this->sAuthMethod = $sAuthMethod;
	}

	function getErrorPage() {
		return $this->sErrorPage;
	}
	function setErrorPage($sErrorPage) {
        //        if ((errorPage == null) || !errorPage.startsWith("/"))
        //            throw new IllegalArgumentException
        //                ("Error Page resource path must start with a '/'");
		#$this->sErrorPage = RequestUtil.URLDecode($sErrorPage);
		$this->sErrorPage = $sErrorPage;
	}

	function getLoginPage() {
		return $this->sLoginPage;
	}
	function setLoginPage($sLoginPage) {
        //        if ((loginPage == null) || !loginPage.startsWith("/"))
        //            throw new IllegalArgumentException
        //                ("Login Page resource path must start with a '/'");
		#$this->sLoginPage = RequestUtil.URLDecode($sLoginPage);
		$this->sLoginPage = $sLoginPage;
	}

	function getRealmName() {
		return $this->sRealmName;
	}

	function setRealmName($sRealmName) {
		$this->realmName = $sRealmName;
	}


	// ----- Constructor ---------------------------------------------------- //

	/**
	* Construct a new LoginConfig with the specified properties.
	*
	* @param string	$sAuthMethod - The authentication method
	* @param string	$sRealmName - The realm name
	* @param string	$sLoginPage - The login page URI
	* @param string	$sErrorPage - The error page URI
	*/
	function LoginConfig($sAuthMethod='', $sRealmName='', $sLoginPage='', $sErrorPage='') {

		$this->setAuthMethod($sAuthMethod);
		$this->setRealmName($sRealmName);
		$this->setLoginPage($sLoginPage);
		$this->setErrorPage($sErrorPage);

	}


	// ----- Public Methods ------------------------------------------------- //

	/**
	* Return a String representation of this object.
	*/
	function toString() {

		$sb = 'LoginConfig[';
		$sb .= 'authMethod=';
		$sb .= $this->sAuthMethod;
		if($this->sRealmName != '') {
			$sb .= ", realmName=";
			$sb .= $this->sRealmName;
		}
		if($this->sLoginPage != '') {
			$sb .= ", loginPage=";
			$sb .= $this->sLoginPage;
		}
		if($this->sErrorPage != '') {
			$sb .= ", errorPage=";
			$sb .= $this->sErrorPage;
		}
		$sb .= "]";
		return $sb;

	}

}
?>