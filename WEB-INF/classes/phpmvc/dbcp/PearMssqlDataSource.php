<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/classes/phpmvc/dbcp/PearMssqlDataSource.php,v 1.2 2006/02/22 08:24:19 who Exp $
* $Revision: 1.2 $
* $Date: 2006/02/22 08:24:19 $
*
* ====================================================================
*
* License:	GNU Lesser General Public License (LGPL)
*
* Copyright (c) 2004-2006 John C.Wildenauer.  All rights reserved.
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
* A class that provides an interface to the Pear::DB::MSSQL Server database 
*	abstraction classes.
*
* @author John C. Wildenauer<br>
*	Dave Halsted (MSSQL revision and testing)
*
* @version $Revision: 1.2 $
* @public
*/
class PearMssqlDataSource extends DB_mssql {

	// ----- Properties ----------------------------------------------------- //

	var $phptype		= ''; 	// Database backend (mysql, ...). Not reqd here.
	var $dbsyntax		= ''; 	// Database used with regards to SQL syntax etc.
	var $protocol		= ''; 	// Communication protocol to use (tcp, unix etc.)
	var $hostspec		= ''; 	// Host specification (hostname[:port])
	var $port			= ''; 	// Port specification (default MySQL port is 3306)
	var $database		= ''; 	// Database to use on the DBMS server
	var $username		= ''; 	// User name for login
	var $password		= ''; 	// Password for login
	var $persistent	= False;	// Boolean


	// Override PEAR::DB_common() __sleep() function:
	// PEAR::DB_common() defines a  __sleep() function to save the PEAR::DB specific
	// properties. So we need to "override" the PEAR  __sleep() definition here.
	//
	// PHP Manual::Ch 9. Classes and Objects (PHP 4)::The magic functions __sleep and __wakeup.
	// serialize() checks if your class has a function with the magic name __sleep. If so, 
	// that function is run prior to any serialization. It can clean up the object and 
	// is supposed to return an array with the names of all variables of that object that 
	// should be serialized. 
	// unserialize() checks for the presence of a function with the magic name __wakeup. 
	function __sleep() {
		$refThisClass =& $this;
		return( array_keys( get_object_vars( $refThisClass ) ) );
	}


	// Setters. ( set[PropertyToSet]($value) )
	// <set-property property = "username"   value = "xxxxxxxx"/>

	/**
	* Database used with regards to SQL syntax etc.
	* <p><pre>&lt;set-property property = "dbsyntax"   value = "xxxxxxxx"/&gt;</pre>
	*
	* @param string	Database used with regards to SQL syntax etc.
	* @returns void
	*/
	function setDbsyntax($value) {
		$this->dbsyntax = $value;
	}

	/**
	* Communication protocol to use (tcp, unix etc.)
	*
	* @param string	The communication protocol to use (tcp, unix etc.)
	* @returns void
	*/
	function setProtocol($value) {
		$this->protocol = $value;
	}

	/**
	* Host specification (hostname[:port])
	*
	* @param string	The hostname[:port] for connection to the DBMS.
	* @returns void
	*/
	function setHost($value) {
		$this->hostspec = $value;
	}

	/**
	* Port specification (default MySQL port is 3306)
	*
	* @param string	The port number for connection to the DBMS.
	* @returns void
	*/
	function setPort($value) {
		$this->port = $value;
	}

	/**
	* Database to use on the DBMS server
	*
	* @param string	The database to use on the DBMS server.
	* @returns void
	*/
	function setDatabase($value) {
		$this->database = $value;
	}

	/**
	* User name for login
	*
	* @param string	The username for this connection.
	* @returns void
	*/
	function setUsername($value) {
		$this->username = $value;
	}

	/**
	* Password for login
	*
	* @param string	The password for this connection.
	* @returns void
	*/
	function setPassword($value) {
		$this->password = $value;
	}

	/**
	* Persistent, expects a boolean (Default is False)
	*
	* @param string	Whether to use a persistent connection.
	* @returns void
	*/
	function setPersistent($value) {
		if(strtolower($value) == 'true') {
			$this->persistent = True;
		}
	}


// ----- Public Methods ---------------------------------------------------- //

	/**
	* Setup the database server connection.
	*<p>Returns immediately if a connection already exists.
	*<p>Note: It is assumed that this object has been pre-configured via the
	* initialisation process.
	*
	* @returns void
	*/
	function open() {
		
		if($this->connection != '') {
			// We already have a valid connection
			return;
		}

		$dsninfo = array();
		$dsninfo['dbsyntax'] = $this->dbsyntax;
		$dsninfo['protocol'] = $this->protocol;	
		$dsninfo['hostspec'] = $this->hostspec;
		$dsninfo['port']		= $this->port;
		$dsninfo['database'] = $this->database;
		$dsninfo['username'] = $this->username;
		$dsninfo['password'] = $this->password;
		$persistent				= $this->persistent;
		
		$conn = $this->connect($dsninfo, $persistent);
		
		// Do we have a vaild connection
		if( DB::isError($conn) ) {
			die( $conn->getMessage() );
		}
	}

}
?>