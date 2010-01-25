<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/classes/phpmvc/dbcp/BasicDataSource.php,v 1.3 2006/02/22 07:05:09 who Exp $
* $Revision: 1.3 $
* $Date: 2006/02/22 07:05:09 $
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
* <p>Basic implementation of a <code>DataSource</code> that is
* configured via phpBeans properties.</p>
*
* @author John C Wildenauer (php.MVC port)<br>
*  Credits:<br>
*  Glenn L. Nielsen (original Jakarta commons class: see jakarta.apache.org)<br>
*  Craig R. McClanahan (original Jakarta commons class: see jakarta.apache.org)
* @version $Revision: 1.3 $
* @public
*/
class BasicDataSource {

	// ----- Instance Variables --------------------------------------------- //

	/**
	* The object pool (GenericObjectPool !!!) that internally manages our 
	* connections.
	* @private
	* @type GenericObjectPool
	*/
	var $connectionPool = NULL;


	/**
	* The connection properties that will be sent to our JDBC driver when
	* establishing new connections.  <strong>NOTE</strong> - The "user" and
	* "password" properties will be passed explicitly, so they do not need
	* to be included here.
	* @private
	* @type array
	*/
	var $connectionProperties = array(); // new Properties()


	/**
	* The data source we will use to manage connections.  This object should
	* be acquired <strong>ONLY</strong> by calls to the
	* <code>createDataSource()</code> method.
	* @private
	* @type DataSource
	*/
	var $dataSource = NULL; // 	DataSource


	/**
	* The PrintWriter to which log messages should be directed. !!!
	* @private
	* @type PrintWriter
	*/
	var $logWriter = NULL; // new PrintWriter(System.out)


	// ----- Protected Properties ------------------------------------------- //

	/**
	* The default auto-commit state of connections created by this pool.
	* @private
	* @type boolean
	*/
	var $defaultAutoCommit = True;

	/**
	* The default read-only state of connections created by this pool.
	* @private
	* @type boolean
	*/
	var $defaultReadOnly = False;

	/**
	* The fully qualified class name of the DB driver to be used.
	* @private
	* @type string
	*/
	var $driverClassName = NULL;

	/**
	* The datasource description
	* @private
	* @type string
	*/
	var $driverDescription = NULL;

	/**
	* The maximum number of active connections that can be allocated from
	* this pool at the same time, or zero for no limit.
	* @private
	* @type int
	*/
	var $maxActive = 4; // GenericObjectPool.DEFAULT_MAX_ACTIVE

	/**
	* The maximum number of active connections that can remain idle in the
	* pool, without extra ones being released, or zero for no limit.
	* @private
	* @type int
	*/
	var $maxIdle = 3; // GenericObjectPool.DEFAULT_MAX_IDLE;

	/**
	* The maximum number of milliseconds that the pool will wait (when there
	* are no available connections) for a connection to be returned before
	* throwing an exception, or -1 to wait indefinitely. !!!!!!!!!!
	* @private
	* @type int
	*/
	var $maxWait = 1000; //  !!!!!!!!!!! GenericObjectPool.DEFAULT_MAX_WAIT;

	/**
	* The connection username to be passed to our JDBC driver to
	* establish a connection.
	* @private
	* @type string
	*/
	var $username = NULL;

	/**
	* The connection password to be passed to our DB driver to establish
	* a connection.
	* @private
	* @type string
	*/
	var $password = NULL;

	/**
	* The connection URL to be passed to our DB driver to establish
	* a connection.
	* @private
	* @type string
	*/
	var $url = NULL;

	/**
	* The SQL query that will be used to validate connections from this pool
	* before returning them to the caller.  If specified, this query
	* <strong>MUST</strong> be an SQL SELECT statement that returns at least
	* one row.
	* @private
	* @type string
	*/
	var $validationQuery = NULL;


	// ----- Properties ----------------------------------------------------- //


	/**
	* @public
	* @returns boolean
	*/
	function getDefaultAutoCommit() {
		return $this->defaultAutoCommit;
	}

	/**
	* @param boolean
	* @public
	* @returns void
	*/
	function setDefaultAutoCommit($defaultAutoCommit) {
		$this->defaultAutoCommit = $defaultAutoCommit;
	}


	/**
	* @public
	* @returns boolean
	*/
	function getDefaultReadOnly() {
		return $this->defaultReadOnly;
	}

	/**
	* @param boolean
	* @public
	* @returns void
	*/
	function setDefaultReadOnly($defaultReadOnly) {
		$this->defaultReadOnly = $defaultReadOnly;
	}


	/**
	* @public
	* @returns string
	*/
	function getDriverClassName() {
		return $this->driverClassName;
	}

	/**
	* @param string
	* @public
	* @returns void
	*/
	function setDriverClassName($driverClassName) {
		$this->driverClassName = $driverClassName;
	}


	/**
	* @public
	* @returns string
	*/
	function getDriverDescription() {
		return $this->driverDescription;
	}

	/**
	* @param string
	* @public
	* @returns void
	*/
	function setDriverDescription($description) {
		$this->driverDescription = $description;
	}


	/**
	* @public
	* @returns int
	*/
	function getMaxActive() {
		return $this->maxActive;
	}

	/**
	* @param int
	* @public
	* @returns void
	*/
	function setMaxActive($maxActive) {
		$this->maxActive = $maxActive;
	}


	/**
	* @public
	* @returns int
	*/
	function getMaxIdle() {
		return $this->maxIdle;
	}

	/**
	* @param int
	* @public
	* @returns void
	*/
	function setMaxIdle($maxIdle) {
		$this->maxIdle = $maxIdle;
	}


	/**
	* @public
	* @returns int
	*/
	function getMaxWait() {
		return $this->maxWait;
	}

	/**
	* @param int
	* @public
	* @returns void
	*/
	function setMaxWait($maxWait) {
		$this->maxWait = $maxWait;
	}


	/**
	* [Read Only] The current number of active connections that have been
	* allocated from this data source.
	* @public
	* @returns int
	*/
	function getNumActive() {
		return NULL;
	#	if(connectionPool != null) {
	#		return (connectionPool.getNumActive());
	#	} else {
	#		return (0);
	#	}
	}


	/**
	* [Read Only] The current number of idle connections that are waiting
	* to be allocated from this data source.
	* @public
	* @returns int
	*/
	function getNumIdle() {
		return NULL;
	#	if(connectionPool != null) {
	#		return (connectionPool.getNumIdle());
	#	} else {
	#		return (0);
	#	}
	}


	/**
	* @public
	* @returns string
	*/
	function getUsername() {
		return $this->username;
	}

	/**
	* @param string
	* @public
	* @returns void
	*/
	function setUsername($username) {
		$this->username = $username;
	}


	/**
	* @public
	* @returns string
	*/
	function getPassword() {
		return $this->password;
	}

	/**
	* @param string
	* @public
	* @returns void
	*/
	function setPassword($password) {
		$this->password = $password;
	}


	/**
	* @public
	* @returns string
	*/
	function getUrl() {
		return $this->url;
	}

	/**
	* @param string
	* @public
	* @returns void
	*/
	function setUrl($url) {
		$this->url = $url;
	}


	/**
	* @public
	* @returns string
	*/
	function getValidationQuery() {
		return $this->validationQuery;
	}

	/**
	* @param string
	* @public
	* @returns void
	*/
	function setValidationQuery($validationQuery) {
		$this->validationQuery = $validationQuery;
	}


	// ----- DataSource Methods --------------------------------------------- //

	/**
	* Create (if necessary) and return a connection to the database.
	*
	* @public
	* @returns Connection
	*/
	#function getConnection() {
	#
	#	$ds = $this->createDataSource();
	#	return $ds->getConnection();
	#
	#}


	/**
	* Create (if necessary) and return a connection to the database.
	*
	* @param string	The database user for this Connection
	* @param string	The database user's password
	* @public
	* @returns Connection
	*/
	function getConnection($username='', $password='') {

		$ds = $this->createDataSource();
		return $ds->getConnection($username, $password);
	}


	// ----- Public Methods ------------------------------------------------- //

	/**
	* Add a custom connection property to the set that will be passed to our
	* JDBC driver.    This <strong>MUST</strong> be called before the first
	* connection is retrieved (along with all the other configuration
	* property setters).
	*
	* @param string	The name of the custom connection property
	* @param string	The value of the custom connection property
	* @public
	* @returns void
	*/
	function addConnectionProperty($name, $value) {

		$this->connectionProperties[$name] = $value;

	}


	/**
	* Close and release all connections that are currently stored in the
	* connection pool associated with our data source.
	*
	* @public
	* @returns void
	*/
	function close() {}


	// ----- Protected Methods ---------------------------------------------- //

	/**
	* <p>Create (if necessary) and return the internal data source we are
	* using to manage our connections.</p>
	*
	*
	* @private
	* @returns DataSource
	*/
	function createDataSource() {

		// Return the pool if we have already created it
		if($this->dataSource != NULL) {
			return $this->dataSource;
		}

		// Load the JDBC driver class
		$driverClass = NULL;	// Class

		// Include the DB driver class file
		$driverClassFile = $this->driverClassName.'.php';
		include_once $driverClassFile;

		// Instantiate the DB driver class
		$driverClass = new $driverClassName;

		// Catch
		if($driverClass == NULL) {
			$message = "Cannot load Database driver class '" .
									$this->driverClassName . "'";
			print $message;	// setup the log !!!
			return;
		}


		// Set up the driver connection factory we will use
		# !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
		if($this->username != NULL) {
			$this->connectionProperties['user'] = $this->username;
		} else {
			#System.out.println("DBCP DataSource configured without a 'username'");
			print "DBCP DataSource configured without a 'username'";
		}
		if($this->password != NULL) {
			$this->connectionProperties['password'] = $this->password;
		} else {
			#System.out.println("DBCP DataSource configured without a 'password'");
			print "DBCP DataSource configured without a 'password'";
		}
		

		// Create and return the pooling data source to manage the connections
		#dataSource = new PoolingDataSource(connectionPool);
		#dataSource.setLogWriter(logWriter);
		
		#return $dataSource;
		return;	// $dataSource !!!

	}

}
?>