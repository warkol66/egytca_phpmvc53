<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/classes/phpmvc/authenticator/ServiceAuthenticator.php,v 1.2 2006/05/21 22:28:24 who Exp $
* $Revision: 1.2 $
* $Date: 2006/05/21 22:28:24 $
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
* An <b>Authenticator</b> implementation that handles a non-interactive logon.
* For example, this authenticator could be used when an automated job daemon
* (say a Unix cron job) requests an application URL that may produce and send
* sales reports an a regular basis.
*
* Ref: Tomcat-catalina.authenticator.<br>
* Credits: Craig R. McClanahan, Remy Maucherat<br>
*
* @author John C Wildenauer
* @version $Revision: 1.2 $
* @public
*/
class ServiceAuthenticator extends AuthenticatorBase {

	// ----- Instance Variables --------------------------------------------- //

	/**
	* Descriptive information about this implementation.
	* @type string
	*/
	var $sInfo = 'php.MVC.authenticator.ServiceAuthenticator/1.0';

	/**
	* Character encoding to use to read the username and password parameters
	* from the request. If not set, the encoding of the request body will be
	* used.
	* @type string
	*/
	var $sCharacterEncoding = '';


	// ----- Public Properties ---------------------------------------------- //

	/**
	* Return descriptive information about this Valve implementation.
	*/
	function getInfo() {
		return $this->sInfo;
	}

	/**
	* Return the character encoding to use to read the username and password.
	*/
	function  getCharacterEncoding() {
		return $this->sCharacterEncoding;
	}

	/**
	* Set the character encoding to be used to read the username and password. 
	*/
	function  setCharacterEncoding($sCharacterEncoding) {
		$this->sCharacterEncoding = $sCharacterEncoding;
	}


	// ----- Constructor ---------------------------------------------------- //

	function ServiceAuthenticator() {

		// Setup the parent object first
		#parent::__construct();	// PHP5
		parent::AuthenticatorBase();

	}


	// ----- Public Methods ------------------------------------------------- //

	/**
	* Authenticate the user (service) making this request, based on the 
	* specified login configuration. Return <code>True</code> if any specified
	* constraint has been satisfied, or <code>False</code> on failure to
	* authenticate.
	*
	* @param Request		Request we are processing
	* @param Response		Response we are creating
	* @param LoginConfig	The LoginConfig configuration describing how 
	*              			authentication should be performed.
	* @returns boolean
	*/
	function authenticate(&$request, &$response, $oConfig) {

		// References to objects we will need later
		$oSession = Null;

		// Have we already authenticated someone?
		$oPrincipal = $request->getUserPrincipal();	// Principal
		#String ssoId = (String) request.getNote(Constants.REQ_SSOID_NOTE);
		if($oPrincipal != Null) {
			if($this->log->getLog('isDebugEnabled')) {
				$this->log->debug("Already authenticated '".$oPrincipal.getName()."'");
			}
			// Associate the session with any existing SSO session
			#if(ssoId != Null) {
			#	$this->associate(ssoId, $request.getSessionInternal(True));
			#	return True;
			#}
		}

		// Yes -- Validate the specified credentials and redirect
		// to the error page if they are not correct
		$oRealm = $this->oContext->getRealm();		// Realm
		if($this->sCharacterEncoding != '') {
			$request.setCharacterEncoding($this->sCharacterEncoding);
		}

		$sUsername = $request->getParameter(PhpMVC_Auth_Const::getKey('FORM_USERNAME'));
		$sPassword = $request->getParameter(PhpMVC_Auth_Const::getKey('FORM_PASSWORD'));
		if($this->log->getLog('isDebugEnabled')) {
			$this->log->debug("Authenticating username: '".$sUsername . "'");
		}

		$oPrincipal = $oRealm->authenticate($sUsername, $sPassword);

		// Write log error and return False if there is a problem creating the 
		// user Principal. In a normal interactive login we would forward the
		// user to a login error page.
		if($oPrincipal == Null) {
			if($this->log->getLog('isErrorEnabled')) {
				$msg = $this->pmr->getMessage( $this->locale, 
														'authenticator.principalCreateErr',
														'', get_class($this) );
				$this->log->error("Authentication error:", $msg);
			}
			return False;
		}

		// Save the authenticated Principal on our request object
		$request->setAttribute(PhpMVC_Auth_Const::getKey('REQ_PRINCIPAL_NOTE'), $oPrincipal);

		// Session handling as required:
		// Save the authenticated Principal in our session
		#$session->setNote(PhpMVC_Auth_Const::getKey('FORM_PRINCIPAL_NOTE'), $oPrincipal);
		
		// Save the username and password as well. 
		// (JCW: Really save the user plaintext pasword in a session !!!)
		#$session->setNote(PhpMVC_Auth_Const::getKey('SESS_USERNAME_NOTE'), $sUsername);
		#$session->setNote(PhpMVC_Auth_Const::getKey('SESS_PASSWORD_NOTE'), $sPassword);

		// Redirect the user to the original request URI:
		// There is no login page for a service authentication (non-interactive logon), 
		// so we just return True (auth=success) and garnt permission to access the 
		// resourses guarded by this authenticator.
		return True;	// 

	}
}

?>