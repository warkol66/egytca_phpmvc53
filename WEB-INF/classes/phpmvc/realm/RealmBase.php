<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/classes/phpmvc/realm/RealmBase.php,v 1.1 2006/05/17 08:02:13 who Exp $
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
* RealmBase.
* An abstract implementation of <b>Realm</b>.
*
* Ref: Tomcat-catalina.realm.RealmBase.<br>
* Credits: Craig R. McClanahan<br>
*
* @author John C Wildenauer
* @version $Revision: 1.1 $
* @public
*/
class RealmBase  {

	/**
	* Loging class
	* @type PhpMVC_Log
	*/
	var $log = Null;

	// PropertyMessageResources class handles message string resources
	var $pmr = Null;

	// Users locale
	var $locale = Null;			// No user locale supplied


	// ----- Protected Properties ------------------------------------------- //

	/**
	* Digest algorithm used in storing passwords in a non-plaintext format.
	* @type string
	*/
	var $digest = 'MD5';

	/**
	* Descriptive information about this <code>Realm</code> implementation.
	* @type string
	*/
	var $info = 'php.MVC.realm.RealmBase/1.0';


	/**
	* Return the digest algorithm used for storing credentials.
	* @returns string
	*/
	function getDigest() {
		return $this->digest;
	}

	/**
	* Set the digest algorithm used for storing credentials.
	*
	* @param string The new digest algorithm
	* @returns void
	*/
	function setDigest($digest) {
		$this->digest = $digest;
	}

	/**
	* Return descriptive information about this Realm implementation and
	* the corresponding version number, in the format
	* <code>&lt;description&gt;/&lt;version&gt;</code>.
	* @returns string
	*/
	function getInfo() {
		return $this->info;
	}


	// ----- Constructor ------------------------------------------------- //

	function RealmBase() {
	
		$this->log = new PhpMVC_Log();
		$this->log->setLog('isDebugEnabled'	, False);

		// Base name of the "RealmLocalStrings.properties" file
		$config = 'RealmLocalStrings';
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
	* Return the Principal associated with the specified username and
	* credentials, if there is one; otherwise return <code>Null</code>.
	*
	* @param username		Username of the Principal to look up.
	* @param credentials	Password or other credentials to use in authenticating 
	* 							this username.
	* @returns Principal
	*/
	function authenticate($username, $credentials) {

		$serverCredentials = '';
		$serverCredentials = $this->getPassword($username);

		if( ($serverCredentials == Null) || (!$serverCredentials == $credentials) ) {
			return Null;
		}

		$oPrincipal = $this->getPrincipal($username);

		return $oPrincipal;

	}


	/**
	* Return <code>True</code> if the specified Principal has the specified
	* security role, within the context of this Realm, otherwise return
	* <code>False</code>.  This method can be overridden by Realm
	* implementations, but the default is adequate when an instance of
	* <code>GenericPrincipal</code> is used to represent authenticated
	* Principals from this Realm.
	*
	* @param Principal	Principal for whom the role is to be checked.
	* @param string		Security role to be checked.
	* @returns boolean
	*/
	function hasRole($principal, $sRole) {

		// Make PHP5 compliant
		$fGP = Null;		// boolean flag
		if((int)phpversion() == 4) {
			$fGP = is_a($principal, 'GenericPrincipal');
		} elseif((int)phpversion() >= 5) {
			#if($principal instanceof GenericPrincipal) {	// << instanceof not known by PHP4
			if(is_a($principal, 'GenericPrincipal')) {
			   $fGP = True;
			} else {
				$fGP = False;
			}
		}

		if(($principal == Null) || ($sRole == Null) || ($fGP == false) ) {
			return False;
		}

		$oGP = $principal;

		if($oGP->getRealm() != $this) {	// Compare realm objects
			// get_class($this) => PHP4='realmbase'; PHP5='RealmBase'
			if($this->log->getLog('isDebugEnabled')) {
				$this->log->debug("Different realm: ".get_class($this)." <> ".
											get_class($oGP->getRealm()) );
			}
		}

		$fResult = Null; 	// boolean flag
		$fResult = $oGP->hasRole($sRole);
		if($this->log->getLog('isDebugEnabled')) {
			$sName = $principal->getName();
			if($fResult) {
				$msg = $this->pmr->getMessage($this->locale, 'realmBase.hasRoleSuccess',
															'', $sName, $sRole);
			} else {
				$msg = $this->pmr->getMessage($this->locale, 'realmBase.hasRoleFailure',
															'', $sName, $sRole);
			}
			$this->log->debug($msg);
		}
		
		return $fResult;

	}


	/**
	* Create a hash digest of the user password using the specified algorithm [md5].
	* The returned value is a hexadecimal string.
	*
	* The plaintext password is returned unchanged if no hash digest method has been 
	* defined.
	*
	* @param string	The password or other credentials to use in authenticating
	*						this username.
	* @returns string
	*/
	function digest($sCredentials)  {

        // If no hash method is specified, return the plaintext password unchanged
			if($this->digest == '') {
				return $sCredentials;
			}

			// Digest the user credentials and return as hexadecimal string	
			$shCredentials = '';
			if($this->digest == 'MD5') {
				$shCredentials = md5($sCredentials);	// The hash is a 32-character hex number
			} elseif($this->digest == 'SHA1') {
				// SHA1 ...
			} else {
				// Cannot find a hash digest, so return the plaintext password unchanged
				$shCredentials = $sCredentials;
			}

			return $shCredentials;							// The credentials as a digested hex string

	}


	// ----- Abstract Protected Methods ------------------------------------- //

	/**
	* Return a short name for this Realm implementation, for use in
	* log messages.
	* @returns string
	*/
	function getName() {}

	/**
	* Return the password associated with the given principal's user name.
	* @param string	Username of the Principal to look up.
	* @returns string
	*/
	function getPassword($username) {}

	/**
	* Return the Principal associated with the given user name.
	* @param string	Username of the Principal to look up.
	* @returns Principal
	*/
	function getPrincipal($username) {}

}
?>