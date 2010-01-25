<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/classes/phpmvc/realm/DataSourceRealm.php,v 1.2 2006/05/21 22:37:41 who Exp $
* $Revision: 1.2 $
* $Date: 2006/05/21 22:37:41 $
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
* Implmentation of <code>Realm</code> that works with any php.MVC supported database.
*
* Ref: Tomcat-catalina.realm.JDBCRealm.java.<br>
* Credits: Craig R. McClanahan, Carson McDonald, Ignacio Ortega<br>
*          
* @author John C Wildenauer
* @version $Revision: 1.2 $
* @public
*/
class DataSourceRealm extends RealmBase {

	// ----- Protected Instance Variables ----------------------------------- //

	/**
	* The connection username to use for connection to the database.
	* @type string
	*/
	var $sConnName = '';

	/**
	* The connection password to use for connection to the database.
	* @type string
	*/
	var $sConnPword = '';

	/**
	* The connection URL to use for connection to the database.
	* @type string
	*/
	var $sConnURL = '';

	/**
	* The connection to the database.
	* @type object
	*/
	var $oDbConn = Null;

	#/**
	#* Instance of the JDBC Driver class we use as a connection factory.
	#*/
	#protected Driver driver = null;

	/**
	* Descriptive information about this Realm implementation.
	* @type string
	*/
	var $info = 'php.MVC.realm.DataSourceRealm/1.0';

	/**
	* Descriptive information about this Realm implementation.
	* @type string
	*/
	var $name = 'DataSourceRealm';

	/**
	* The column in the user role table that names a role
	* @type string
	*/
	var $roleNameCol = 'role_name';

	/**
	* The column in the user table that holds the user's credintials
	* @type string
	*/
	var $userCredCol = 'user_pass';

	/**
	* The column in the user table that holds the user's name
	* @type string
	*/
	var $userNameCol = 'user_name';

	/**
	* The column in the user table that holds the user password salt value, if any.
	* @type string
	*/
	var $userSaltCol = 'user_salt';

	/**
	* The table that holds the relation between user's and roles
	* @type string
	*/
	var $userRoleTable = 'user_roles';

	/**
	* The table that holds user data.
	* @type string
	*/
	var $userTable = 'users';

	/**
	* The name of the database connection driver class to use.
	* @type string
	*/
	var $connDBDriver = 'PearMysqlDataSource';

	/**
	* The authorisation database to use.
	* @typestring
	*/
	var $authDB = 'phpmvc_auth';			// Like: 'phpmvc_auth'

	/**
	* The database connection mode.
	* @type boolean
	*/
	var $fPersistent = False;

	/**
	* Defines whether the user logon password has been salted.
	* 
	* <p>Salting passwords:<br>
	* Salting the password is intended to increase the difficulty of an attack
	* on this authentication scheme, as the salt value is not known to the attacker. 
	* Salting increases the complexity of a dictionary attack by increasing the 
	* variations of each trial password (greater memory requirements and more
	*  preperation time).
	* 
	* <p>Salting passwords causes two identical user passwords to map to different
	* key space.
	* 
	* @type boolean
	*/
	var $fIsPWSalted = False;


	// ----- Properties ----------------------------------------------------- //

	/**
	* Return the username to use to connect to the database.
	*
	* @returns string
	*/
	function getConnectionName() {
	return $this->sConnName;
	}

	/**
	* Set the username to use to connect to the database.
	*
	* @param connectionName Username
	* @returns void
	*/
	function setConnectionName($sConnName) {
		$this->sConnName = $sConnName;
	}

	/**
	* Return the password to use to connect to the database.
	*
	* @returns string
	*/
	function getConnectionPassword() {
		return $this->sConnPword;
	}

	/**
	* Set the password to use to connect to the database.
	*
	* @param connectionPassword User password
	* @returns void
	*/
	function setConnectionPassword($sConnPword) {
		$this->sConnPword = $sConnPword;
	}

	/**
	* Return the URL to use to connect to the database.
	*
	* @returns string
	*/
	function getConnectionURL() {
		return $this->sConnURL;
	}

	/**
	* Set the URL to use to connect to the database.
	*
	* @param connectionURL The new connection URL
	* @returns void
	*/
	function setConnectionURL($sConnURL) {
		$this->sConnURL = $sConnURL;
	}

	/**
	* Return the column in the user role table that names a role.
	*
	* @returns string
	*/
	function getRoleNameCol() {
		return $this->roleNameCol;
	}

	/**
	* Set the column in the user role table that names a role.
	*
	* @param string The column name
	* @returns void
	*/
	function setRoleNameCol($roleNameCol) {
		$this->roleNameCol = $roleNameCol;
	}

	/**
	* Return the column in the user table that holds the user's credentials.
	*
	* @returns string
	*/
	function getUserCredCol() {
		return $this->userCredCol;
	}

	/**
	* Set the column in the user table that holds the user's credentials.
	*
	* @param string The column name
	* @returns void
	*/
	function setUserCredCol($userCredCol) {
		$this->userCredCol = $userCredCol;
	}

	/**
	* Return the column in the user table that holds the user's name.
	*
	* @returns string
	*/
	function getUserNameCol() {
		return $this->userNameCol;
	}

	/**
	* Set the column in the user table that holds the user's name.
	*
	* @param string The column name
	* @returns void
	*/
	function setUserNameCol($userNameCol) {
		$this->userNameCol = $userNameCol;
	}

	/**
	* Return the column in the user table that holds the user password salt
	* value, if any.
	*
	* @returns string
	*/
	function getUserSaltCol() {
		return $this->userSaltCol;
	}

	/**
	* Set the column in the user table that holds the user password salt
	* value, if any.
	*
	* @param string The column name
	* @returns void
	*/
	function setUserSaltCol($userSaltCol) {
		$this->userSaltCol = $userSaltCol;
	}

	/**
	* Return the table that holds the relation between user's and roles.
	*
	* @returns string
	*/
	function getUserRoleTable() {
		return $this->userRoleTable;
	}

	/**
	* Set the table that holds the relation between user's and roles.
	*
	* @param string The table name
	* @returns void
	*/
	function setUserRoleTable($userRoleTable) {
		$this->userRoleTable = $userRoleTable;
	}

	/**
	* Return the table that holds user data..
	*
	* @returns string
	*/
	function getUserTable() {
		return $this->userTable;
	}

	/**
	* Set the table that holds user data.
	*
	* @param string The table name
	* @returns void
	*/
	function setUserTable($userTable) {
		$this->userTable = $userTable;
	}

	/**
	* Get the name of the database connection driver class to use.
	*
	* @returns string
	*/
	function getConnDBDriver() {
		return $this->connDBDriver;
	}

	/**
	* Set the name of the database connection driver class to use.
	*
	* @param string The table name
	* @returns void
	*/
	function setConnDBDriver($connDBDriver) {
		$this->connDBDriver = $connDBDriver;
	}

	/**
	* Get the authorisation database to use.
	*
	* @returns string
	*/
	function getAuthDB() {
		return $this->authDB;
	}

	/**
	* Set the authorisation database to use.
	*
	* @param string The table name
	* @returns void
	*/
	function setAuthDB($authDB) {
		$this->authDB = $authDB;
	}

	/**
	* Get the database connection mode.
	*
	* @returns string
	*/
	function getPersistent() {
		return $this->fPersistent;
	}

	/**
	* Set the database connection mode.
	*
	* @param string The database ('persistent') connection mode. [False]
	* @returns void
	*/
	function setPersistent($fPersistent=False) {
		$this->fPersistente = $fPersistent;
	}

	/**
	* Get the logon user password salt mode.
	*
	* @returns string
	*/
	function getPWSalted() {
		return $this->fIsPWSalted;
	}

	/**
	* Set the logon user password salt mode.
	*
	* @param string The the logon user password salt mode. [False]
	* @returns void
	*/
	function setPWSalted($fIsPWSalted=False) {
		$this->fIsPWSalted = $fIsPWSalted;
	}

	/**
	* Return a short name for this Realm implementation.
	*/
	#protected String getName() {
	#return (name);
	#}

	/**
	* Return the password associated with the given principal's user name.
	*/
	#protected String getPassword(String username) {
	#return (null);
	#}

	/**
	* Return the Principal associated with the given user name.
	*/
	#protected Principal getPrincipal(String username) {
	#return (null);
	#}


	// ----- Public Methods ------------------------------------------------- //

	/**
	* Return the Principal associated with the specified username and
	* credentials, if there is one. Otherwise return <code>Null</code>.
	*
	* @param string 	Username of the Principal to look up.
	* @param string 	Password or credentials to use in authenticating this 
	*						username.
	*
	* @returns Principal
	*/
	function authenticate($sUsername, $sCredentials) {

		// Return immediately no user is specified
		if($sUsername == Null) {
			return Null;
		}

		// Look up the user's credentials
		$sDbCredentials = '';
		$sDbCredentials = $this->getPassword($sUsername);

		// Check if the user credentials have been salted
		if($this->fIsPWSalted) {
			// Derive the unique MD5 hash key from a supplied password and a salt value.
			$sPWSalt = '';
			$sPWSalt = $this->getPWSalt($sUsername);
			$sCredentials .= $sPWSalt;
		}	

		// Validate the user's credentials
		$fValidated = False;
		if('MD5' == $this->getDigest()) {
			// Hex hashes should be compared case-insensitive
			$fValidated = (strtolower(md5($sCredentials)) == strtolower($sDbCredentials));
		} elseif('SHA1' == $this->getDigest()) {
			//; ...
		} else {
			// Plain text - case senestive password
			$fValidated = $sCredentials == $sDbCredentials;
		}

		// Logging authentication
		if($this->log->getLog('isDebugEnabled')) {
			if($fValidated) {
				$msg = $this->pmr->getMessage($this->locale, 'dscRealm.authenticateSuccess',
															'', $sUsername);
			} else {
				$msg = $this->pmr->getMessage($this->locale, 'dscRealm.authenticateFailure',
															'', $sUsername);
			}
			$this->log->debug($msg);
		}

		if($fValidated == False) {
			// Authentication failed, so were out of here
			return Null;
		}

		$aRoles = array();
		$aRoles = $this->getRoles($sUsername);

		// Create and return a suitable Principal for this user
		$oGP =& new GenericPrincipal($sUsername, $sCredentials, $this, $aRoles);
		return $oGP;

	}


	/**
	* Return the password associated with the given principal's user name.
	* @returns string
	*/
	function getPassword($sUsername) {
	
		$query = 
			"SELECT $this->userCredCol AS credentials ".
				"FROM $this->userTable ".
				"WHERE $this->userNameCol='$sUsername'";

		$nNumberOfTries = 2;
		while($nNumberOfTries > 0) {

			// Ensure that we have an open database connection.
			// Returns immediately if we have an existing connection.
			$pDb = Null;	// Pointer to the database connection instance
			$pDb =& $this->open();
			if($pDb == Null) {
				$nNumberOfTries--;
				continue;
			}

			$pDb->setFetchMode(DB_FETCHMODE_ASSOC);
			$result = $pDb->query($query);
			if (DB::isError($result)) {
				exit($result->getMessage());
			}
			$aRow = $result->fetchRow();	
			if($aRow['credentials'] == '') {
				$sCredentials = '';							// No user credentials
			} else {
				$sCredentials  = $aRow['credentials'];	// Like: "916A392EA8781460F9629B280E9B8974"
				return $sCredentials;
			}

			$nNumberOfTries--;
		}	// while()

		return '';

	}


	/**
	* Return the password salt value associated with the given principal's username.
	* @returns string
	*/
	function getPWSalt($sUsername) {
	
		$query = 
			"SELECT $this->userSaltCol AS salt ".
				"FROM $this->userTable ".
				"WHERE $this->userNameCol='$sUsername'";

		$nNumberOfTries = 2;
		while($nNumberOfTries > 0) {

			// Ensure that we have an open database connection.
			// Returns immediately if we have an existing connection.
			$pDb = Null;	// Pointer to the database connection instance
			$pDb = $this->open();
			if($pDb == Null) {
				$nNumberOfTries--;
				continue;
			}

			$pDb->setFetchMode(DB_FETCHMODE_ASSOC);
			$result = $pDb->query($query);
			if (DB::isError($result)) {
				exit($result->getMessage());
			}
			$aRow = $result->fetchRow();	
			if($aRow['salt'] == '') {
				$sSalt = '';							// No user salt
			} else {
				$sSalt = $aRow['salt'];				// Like: ""
				return $sSalt;
			}

			$nNumberOfTries--;
		}	// while()

		return '';

	}


	/**
	* Return the roles associated with the gven user name.
	*
	* @param string		The username
	* @returns array()
	*/
	function getRoles($sUsername) {

		$query = 
			"SELECT $this->roleNameCol ".
				"FROM $this->userRoleTable ".
				"WHERE $this->userNameCol='$sUsername'";

		$nNumberOfTries = 2;
		while($nNumberOfTries > 0) {

			// Ensure that we have an open database connection.
			// Returns immediately if we have an existing connection.
			$pDb = Null;	// Pointer to the database connection instance
			$pDb =& $this->open();
			if($pDb == Null) {
				$nNumberOfTries--;
				continue;
			}

			$pDb->setFetchMode(DB_FETCHMODE_ASSOC);
			$result = $pDb->getAll($query);
			if (DB::isError($result)) {
				exit($result->getMessage());
			}

			// Returns an array set - something like:
			// array(0 => array('role_name', 'admin0'),
			//			1 => array('role_name', 'admin1'))
			//			2 => array('role_name', 'guest'))
			$aRoleSet =& $result;

			// Accumulate the user's roles
			$aRoleList = array();
			foreach($aRoleSet as $k => $aRole) {
				$aRoleList[] = $aRole['role_name'];
			}

			if(count($aRoleList) > 0) {
				return $aRoleList;
			}

			$nNumberOfTries--;
		}	// while()

        return array();

    }


	/**
	* Open (if necessary) and return a database connection for use by
	* this Realm.
	*
	* @returns object
	*/
	function open() {

		// We already have a valid connection
		if($this->oDbConn != Null) {
			return $this->oDbConn;
		}

		$sDataSource = $this->getConnDBDriver();
		$oDataSource =& new $sDataSource;
		$oDataSource->setHost($this->getConnectionURL());
		$oDataSource->setDatabase($this->getAuthDB());
		$oDataSource->setUsername($this->getConnectionName());
		$oDataSource->setPassword($this->getConnectionPassword());
		$oDataSource->setPersistent($this->getPersistent());	

		$conn = $oDataSource->open();
		// Do we have a vaild connection
		if( DB::isError($conn) ) {
			die( $conn->getMessage() );
		}

		$this->oDbConn =& $oDataSource;	// Save a reference to the DS

		return $this->oDbConn;

	}

}
?>