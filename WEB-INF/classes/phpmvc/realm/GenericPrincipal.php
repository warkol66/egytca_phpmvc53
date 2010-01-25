<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/classes/phpmvc/realm/GenericPrincipal.php,v 1.1 2006/05/17 08:02:13 who Exp $
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
* GenericPrincipal.
*
* A generic implementation of a roles-based security <code>Principal</code> 
* that is available for use by <code>Realm</code> implementations.
*
* Ref: Tomcat-catalina.realm.GenericPrincipal.<br>
* Credits: Craig R. McClanahan<br>
*
* @author John C Wildenauer
* @version $Revision: 1.1 $
* @public
*/
class GenericPrincipal extends Principal {

	// ----- Protected Properties ------------------------------------------- //

	/**
	* The username of the user represented by this Principal.
	* @protected
	* @type string
	*/
	var $sUsername = '';

	/**
	* The authentication credentials for the user represented by
	* this Principal.
	* @protected
	* @type string
	*/
	var $sPassword = '';

	/**
	* The Realm with which this Principal is associated.
	* @protected
	* @type Realm
	*/
	var $oRealm = Null;

	/**
	* The set of roles associated with this user.
	* @protected
	* @type array
	*/
	var $aRoles = array();

	// ----- Public Properties ---------------------------------------------- //

	function getName() {
		return $this->sUsername;
	}

	function getPassword() {
		return $this->sPassword;
	}

	function getRealm() {
		return $this->oRealm;
	}

	function setRealm($oRealm) {
		$this->oRealm = $oRealm;
	}

	function getRoles() {
		return $this->aRoles;
	}


	// ----- Constructor ---------------------------------------------------- //

	/**
	* Construct a new <code>Principal</code>, associated with the specified Realm,
	*  for the specified username and password, with the specified role names
	* (as comma separated strings).
	*
	* @param sUsername		The username of the user represented by this Principal
	* @param sPassword	Credentials used to authenticate this user
	* @param oRealm		The Realm object instance that owns this principal
	* @param sRoles		List of roles (as comma separated strings) possessed 
	*                    by this user
	*/
	function GenericPrincipal($sUsername, $sPassword, $oRealm=Null, $aRoles=Null) {

		// Setup the parent object first
		#parent::__construct();	// PHP5
		parent::Principal();

		$this->oRealm		= $oRealm;
		$this->sUsername	= $sUsername;
		$this->sPassword	= $sPassword;
		$this->aRoles		= $aRoles;

	}


	// ----- Public Methods ------------------------------------------------- //

	/**
	* Does the user represented by this Principal possess the specified role?
	*
	* The <code>sRole</code> string is one element of the comma-delimited list 
	* of security role names that may be defined in the currently executing 
	* action-mapping.<br>
	* Like: &lt action ... roles="guest,moderator,admin" .../&gt
	* 
	* An "*" indicates that all roles are allowed access.
	*
	* @param role Role to be tested.
	* @returns boolean
	*/
	function hasRole($sRole) {

		$sRole = trim($sRole);

		if('' == $sRole) {
			return False;
		}
		if('*' == $sRole) {
			return True;
		}

		// Check if the role argument (eg: 'admin') is contained in set of roles
		// associated with this user. Eg: does this Principal (user) have an 'admin'
		// priviledge item in it's GenericPrincipal::aRoles array.
		if( in_array($sRole, $this->aRoles, True) ) {	// Using "strict" type checking
			return True;
		} else {
			return False;
		}

	}


	/**
	* Return a String representation of this object, which exposes only
	* information that should be public. Eg: The user credentials are not
	* reported.
	* @returns string
	*/
	function toString() {
		$sb = 'GenericPrincipal[';
		$sb .= $this->sUsername;
		$sb .= '(';
		foreach($this->aRoles as $v => $role) {
			$sb .=  $role;
			if($v+1 < count($this->aRoles)) {
				$sb .= ',';
			}
		}
		$sb .= ')]';
		return $sb;
	}

}
?>