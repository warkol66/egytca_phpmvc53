<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/classes/phpmvc/authenticator/AuthenticatorBase.php,v 1.2 2006/05/21 22:23:57 who Exp $
* $Revision: 1.2 $
* $Date: 2006/05/21 22:23:57 $
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
* An abstract <b>Authenticator</b> implementation that enforces the
* <code>Security-Constraint&gt;</code> elements of the web application
* descriptor. Individual implementations of each supported authentication
* method can subclass this base class as required.
* <p>
* <b>USAGE CONSTRAINT</b>:  When this class is utilized, the Context to
* which it is attached must have an associated <b>Realm</b> that can be 
* used for authenticating users and enumerating the roles to which they 
* have been assigned.
*
* Ref: Tomcat-catalina.authenticator.AuthenticatorBase.<br>
* Credits: Craig R. McClanahan<br>
*
* @author John C Wildenauer
* @version $Revision: 1.2 $
* @public
*/
class AuthenticatorBase {

	// ----- Instance Variables --------------------------------------------- //

	/**
	* Loging class
	* @type PhpMVC_Log
	*/
	var $log = Null;
	
	// PropertyMessageResources class handles message string resources
	var $pmr = Null;

	// Users locale
	var $locale = Null;			// No user locale supplied

	// -- cut ---

	/**
	* The Context to which this Valve is attached.
	* @type AppServerContext
	*/
	var $oContext = Null;

	/**
	* Descriptive information about this implementation.
	* @type string
	*/
	var $sInfo = 'php.MVC.authenticator.AuthenticatorBase/1.0';

	// -- cut ---

	/**
	* "Expires" header always set to Date(1), so generate once only.
	* @type string
	*/
	var $sDATE_ONE = '';	// (DateTool.HTTP_RESPONSE_DATE_HEADER, Locale.US)).format(new Date(1))


	// ----- Public Properties ---------------------------------------------- //

	/**
	* Return the AppServerContext instance to which this authorisation instance
	* is attached.
	*/
	function getAppServerContext() {
		return $this->oContext;
	}

	/**
	* Set the AppServerContext instance to which this authorisation instance
	* is attached.
	*/
	function setAppServerContext($oContext) {
		$this->oContext = $oContext;
	}

	/**
	* Return descriptive information about this Valve implementation.
	*/
	function getInfo() {
		return $this->sInfo;
	}


	// ----- Constructor ------------------------------------------------- //

	function AuthenticatorBase() {
	
		$this->log = new PhpMVC_Log();
		$this->log->setLog('isDebugEnabled', False);
		$this->log->setLog('isErrorEnabled', False);

		// Base name of the "AuthLocalStrings.properties" file
		$config = 'AuthLocalStrings';
		$returnNull = False;	// return something like "???message.hello_world???" if we
									// cannot find a message match in any of the properties files.
		$defaultLocale =& new Locale(); // default appServer Locale
		$factory = NULL;		// MessageResources factory classes, skip for now
		$pmr =& new PropertyMessageResources($factory, $config, $returnNull);
		$pmr->setDefaultLocale($defaultLocale);
		$this->pmr = $pmr;

	}


	// ----- Public Methods ------------------------------------------------- //

	/**
	* Enforce the security restrictions in the web application roles action mapping
	*
	* @param Request		Request to be processed
	* @param Response		Response to be processed
	* @returns void
	*/
	function invoke(&$request, &$response, &$appServerContext) {
	
		$oLogonConfig = $appServerContext->getLoginConfig();

		// Authentication method: BASIC, DIGEST, FORM, CLIENT-CERT or SERVICE
		$sAuthMethod = $oLogonConfig->getAuthMethod();	

		// Determine the Authenticator class to use, and create it.
		$oAuthenticator = Null;
		if($sAuthMethod       == 'BASIC') {
			// ;
		} elseif($sAuthMethod == 'DIGEST') {
			// ;
		} elseif($sAuthMethod == 'FORM') {
			// ;
		} elseif($sAuthMethod == 'CLIENT-CERT') {
			// ;
		} elseif($sAuthMethod == 'SERVICE') {
			// A non-interactive type login
			$oAuthenticator = new ServiceAuthenticator();
		} else {
			#print "Fatal error: No valid authentication method set. Shutting down now ...";
			#exit;
			return Null;
		}

		$oAuthenticator->setAppServerContext($appServerContext);

		// Authenticate the user. The user Principal is saved to the request object
		// using the key 'REQ_PRINCIPAL_NOTE'.
		$oGenPrincipal = Null;
		$fRes = $oAuthenticator->authenticate($request, $response, $oLogonConfig);
		if($fRes == True) {
			$oGenPrincipal = 
				$request->getAttribute(PhpMVC_Auth_Const::getKey('REQ_PRINCIPAL_NOTE'));
		}

		return $oGenPrincipal ;

	}


	// ----- Protected Methods ---------------------------------------------- //

	/**
	* Authenticate the user making this request, based on the specified
	* login configuration.  Return <code>True</code> if any specified
	* constraint has been satisfied, or <code>False</code> if we have
	* created a response challenge already.
	*
	* @param Request		Request we are processing
	* @param Response		Response we are creating
	* @param config		LoginConfig configuration describing how authentication
	*							should be performed
	* @returns boolean
	*/
	function authenticate($request, $response, $oConfig) {
		// Create this method in a sub-class
	}

	/**
	* Generate and return a new session identifier for the cookie that
	* identifies an SSO principal.
	* @returns string
	*/
	function generateSessionId() {
		// ...
	}

}
?>