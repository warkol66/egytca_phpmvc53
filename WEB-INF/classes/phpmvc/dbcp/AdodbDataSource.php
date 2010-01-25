<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/classes/phpmvc/dbcp/AdodbDataSource.php,v 1.4 2006/02/22 07:01:42 who Exp $
* $Revision: 1.4 $
* $Date: 2006/02/22 07:01:42 $
*
* ====================================================================
*
* License:	GNU Lesser General Public License (LGPL)
*
* Copyright (c) 2004-2006 John C. Wildenauer. All rights reserved.
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
* A class that provides an interface to the ADODB database abstraction 
*	classes.
* 
* <p>This version of the AdodbDataSource driver provides for the dynamic loading
* of alternative ADODB vendor drivers, for example MySql, MS Access, Postgres etc.
* The ADODB vendor driver to load is defined in the application phpmvc-config.xml
* configuration file using the <code>databaseType</code> property:
* <pre>
* &lt;data-sources&gt;
*   &lt;data-source   
*     key  = "ADODB_MYSQL_DATA_SOURCE"
*     type = "AdodbDataSource"&gt;
*       &lt;set-property property = "host"       value = "localhost"/&gt;
*       <b>&lt;set-property property = "databaseType"   value = "mysql"/&gt;</b>
*       ...
*   &lt;/data-source&gt;
* &lt;/data-sources&gt; 
* </pre>
* </p>
*
* <p><b>Implementation note:</b>
* <ul>
* <li>
*   This version of the AdodbDataSource driver calls the ADODB helper function
*   <code>ADONewConnection("databaseType")</code> to load the defined ADODB vendor 
*   driver.
* <li>
*   This version of the AdodbDataSource driver does not extend any ADODB vendor driver
*   classes, so we must intercept and map the <code>ADOConnection</code> method calls 
*   to provide the ADODB functionality.
* </ul>
* </p>
*
* <p><b>Note:</b> Uncomment "adodb-*.inc.php" lines in 
* <code>php.MVC ./WEB-INF/globalPrepend.php</code> to include the necessary 
* ADODB library files. Add additional <code>include</code> lined to load
* other ADODB RDBM classes as required.</p>
*
* @author John C. Wildenauer, <br>
* Contributors:<br> 
* 	Thai Duong,  <br>
* 	Eric Chang (scrazy AT downput.com), <br>
* 	Ross Lawley
*
* @version $Revision: 1.4 $
* @public
*/
class AdodbDataSource {

	// ----- Instance Variables --------------------------------------------- //

	/** 
	* The record set prefix. 
	* See: Hacking ADOdb Safely - ADOdb Library for PHP Manual::Code Initialization 
	* and Connecting to Databases.
	* @type string
	* @private 
	*/
	var $rsPrefix = 'hack_rs_';

	/** 
	* The ADODB connection instance. Eg: "adodb_mysql"
	* @type Object
	* @private 
	*/
	var $db = NULL;

	/** 
	* Do we have a database connection. True if we have a valid connection, otherwise False.
	* @type boolean
	* @private 
	*/
	var $driverConn = False;


	// ----- Properties ----------------------------------------------------- //

	/**
	* The RDBMS currently in use, eg: odbc, mysql, mssql.
	* @type string
	* @private 
	*/
	var $dbType	= '';

	/**
	* The name of database to be used.
	* @type string
	* @private 
	*/
	var $database		= '';

	/**
	* The hostname of the database server, Usually <code>localhost</code>.
	* @type string 
	* @private 
	*/
	var $host			= '';

	/**
	* The username which is used to connect to the database server.
	* @type string
	* @private 
	*/
	var $username		= '';

	/**
	* The password for the DBMS account.
	* @type string
	* @private 
	*/
	var $password 		= '';	

	/**
	* Whether to use a persistent connection.
	* @type boolean
	* @private 
	*/
	var $persistent	= False;	// Boolean

	/**
	* Support connecting using PEAR style Data Source Names (DNS).
	* <b>EXPERIMENTAL -  USE AT YOUR OWN RISK</b><br>
	* Eg: "driver:/ /username:password@hostname/databasename".
	* Verrrrry slowwww !!!.<br>
	* <b>Note:</b> This requires PEAR to be installed and in the default include
	* path in php.ini.<br>
	* <b>See:</b> ADOdb Library for PHP Manual::Data Source Names
	* @type string
	* @private 
	*/
	var $dsn = '';

	/**
	* Sets the current fetch mode for the connection and stores it in $db->fetchMode.
	* Legal modes are "ADODB_FETCH_ASSOC" and "ADODB_FETCH_NUM".
	* Refer: ADODB manual - ADOConnection::SetFetchMode.
	* @type string
	* @private 
	*/
	var $fetch_Mode = '';	// ADODB defined constant 


	/////
	// Property setters
	// <set-property property = "username"   value = "xxxxxxxx"/>

	/**
	* Set the RDBMS currently in use, eg. odbc, mysql, mssql
	*
	* @param string	The RDBMS currently in use, eg. odbc, mysql, mssql.
	* @public
	* @returns void
	*/
	function setDatabaseType($value) {
		$this->dbType = $value;
	}

	/**
	* Set the name of database to be used.
	*
	* @param string	The name of database to be used.
	* @returns void
	*/
	function setDatabase($value) {
		$this->database = $value;
	}

	/**
	* Set the hostname of the database server.
	*
	* @param string	The hostname of the database server. Usually "localhost"
	* @returns void
	*/
	function setHost($value) {
		$this->host = $value;
	}

	/**
	* Set the username which is used to connect to the database server.
	*
	* @param string	The username which is used to connect to the database server.
	* @returns void
	*/
	function setUsername($value) {
		$this->username = $value;
	}	

	/**
	* Set the Password which is used to connect to the database server.
	*
	* @param string	 Password for this RDBMS account.
	* @returns void
	*/
	function setPassword($value) {
		$this->password = $value;
	}

	/**
	* Persistent, expects a boolean (Default is False)
	*
	* @param boolean	Whether to use a persistent connection.
	* @returns void
	*/
	function setPersistent($value) {
		$this->persistent = $value;
	}

	/**
	* Support connecting using PEAR style DSN's.
	*
	* @param string	"driver:/ /username:password@hostname/databasename"
	* @returns void
	*/
	function setDsn($value)	{
		$this->dsn = $value;
	}

	/**
	* Sets the current fetch mode for the connection and stores it in $db->fetchMode.
	*
	* @param string	The fetch mode to set. "ADODB_FETCH_ASSOC" and "ADODB_FETCH_NUM"
	* @returns void
	*/
	function setFetch_Mode($value)	{
		$this->fetch_Mode = $value;
		
	}


	// ----- Public Methods ------------------------------------------------- //

	/**
	* Setup the database server connection.
	*<p>Returns immediately if a connection already exists.</p>
	*<p>Note: It is assumed that this object has been pre-configured via the
	* initialisation process.</p>
	*
	* @returns void
	*/
	function open() {

		$db	= NULL;	// The database connector class instance, Eg: Object "adodb_mysql"
		$conn = NULL;	// Connect() and PConnect() return True or False

		// Do we already have a valid connection
		if($this->driverConn == True) {
			return;
		}

		// Support connecting using PEAR style Data Source Names (DNS) !!!
		// EXPERIMENTAL. USE AT YOUR OWN RISK
		// Verrrrry slowwww !!!
		if($this->dsn != '') {
			$db =& DB::Connect($this->dsn);	// Eg: Object adodb_mysql 
			// Do we have have a database connector class instance
			//if( DB::isError($db) ) {	// Dont work !!! 
			if( $db == NULL ) {
				die( 'Error: Cannot connect to database using ADODB Data Source Names (DNS) !: ');
			} else {
				$this->driverConn == True;
				$this->db =& $db;	
			}
			return;
		}

		// ADODB helper function
		$db =& ADONewConnection($this->dbType);	
		$this->db =& $db;

		if($this->persistent == True) {
			// Persistent connect to data source or server. Faster than the regular Connect() !!!
			// See: ADOdb Library for PHP Manual::Class Reference::PConnect
			$conn = @$this->db->PConnect($this->host,$this->username,$this->password,$this->database);
		} else {
			$conn = @$this->db->Connect($this->host,$this->username,$this->password,$this->database);
		}

		// Do we have have a valid connection
		if($conn == True) {	
			$this->driverConn == True;
		} else {
			die('Error: Cannot connect to database using ADODB Data Source ! <br>'.
					'Please check that your database server is available, and that your 
					"phpmvc-config.xml" &lt;data-source ...&gt; configuration settings are correct.'
				);
		}


		// Set any ADODB defined constant
		if($this->fetch_Mode == 'ADODB_FETCH_DEFAULT') {
			$this->db->SetFetchMode(ADODB_FETCH_DEFAULT);
		} elseif($this->fetch_Mode == 'ADODB_FETCH_NUM') {
			$this->db->SetFetchMode(ADODB_FETCH_NUM);
		} elseif($this->fetch_Mode == 'ADODB_FETCH_ASSOC') {
			$this->db->SetFetchMode(ADODB_FETCH_ASSOC);
		} elseif($this->fetch_Mode == 'ADODB_FETCH_BOTH') {
			$this->db->SetFetchMode(ADODB_FETCH_BOTH);
		}

	}


	/////
	// ADOConnection class methods.
	// See the ADODB manual for documentation

	// Executing SQL 

	function &Execute($sql, $inputArr=False) {
		$rs =& $this->db->Execute($sql, $inputArr);
		return $rs;
	}

	function &CacheExecute($secs2cache,$sql=false,$inputarr=false) {
		$rs =& $this->db->CacheExecute($secs2cache,$sql,$inputarr);
		return $rs;
	}

	function &ExecuteCursor($sql,$cursorName='rs',$params=false) {
		$rs =& $this->db->ExecuteCursor($sql,$cursorName,$params);
		return $rs;
	}

	function &SelectLimit($sql,$nrows=-1,$offset=-1, $inputarr=false,$secs2cache=0) {
		$rs =& $this->db->SelectLimit($sql,$nrows,$offset,$inputarr,$secs2cache0);
		return $rs;
	}

	function &CacheSelectLimit($secs2cache,$sql,$nrows=-1,$offset=-1,$inputarr=false) {
		$rs =& $this->db->CacheSelectLimit($secs2cache,$sql,$nrows,$offset,$inputarr);
		return $rs;
	}

	function CacheFlush($sql=false,$inputarr=false) {
		$this->db->CacheFlush($sql,$inputarr);
		return;
	}

	function Prepare($sql) {
		return $this->db->Prepare($sql);
	} 

	function PrepareSP($sql,$param=true) {
		return $this->db->PrepareSP($sql,$param);
	} 

	function Parameter(&$stmt,&$var,$name,$isOutput=false,$maxLen=4000,$type=false) {
		return $this->db->Parameter($stmt,$var,$name,$isOutput,$maxLen,$type);
	} 

	function GetOne($sql,$inputarr=false) {
		$res =& $this->db->GetOne($sql,$inputarr);
		return $res;
	}

	function CacheGetOne($secs2cache,$sql=false,$inputarr=false) {
		$res =& $this->db->CacheGetOne($secs2cache,$sql,$inputarr);
		return $res;
	} 

	function &GetRow($sql,$inputarr=false) {
		$res =& $this->db->GetRow($sql,$inputarr);
		return $res;
	}

	function &CacheGetRow($secs2cache,$sql,$inputarr) {
		$res =& $this->db->CacheGetRow($secs2cache,$sql,$inputarr);
		return $res;
	}

	function &GetAll($sql, $inputArray=False) {
		$rs =& $this->db->GetAll($sql, $inputArray);
		return $rs;
	}

	function &CacheGetAll($secs2cache,$sql=false,$inputarr=false) {
		$res =& $this->db->CacheGetAll($secs2cache,$sql,$inputarr);
		return $res;
	}

	function GetCol($sql, $inputarr=false, $trim=false) {
		$res =& $this->db->GetCol($sql,$inputarr, $trim);
		return $res;
	}

	function CacheGetCol($secs, $sql=false, $inputarr=false, $trim=false) {
		$res =& $this->db->CacheGetCol($secs, $sql, $inputarr, $trim);
		return $res;
	}

	function Replace($table, $fieldArray, $keyCol, $autoQuote=false, $has_autoinc=false) {
		$res =& $this->db->Replace($table, $fieldArray, $keyCol, $autoQuote, $has_autoinc);
		return $res;
	}


	// Generates SQL

	function GetUpdateSQL(&$rs, $arrFields,$forceUpdate=false,$magicq=false,$forcenulls=null) {
		$res =& $this->db->GetUpdateSQL($rs, $arrFields,$forceUpdate,$magicq,$forcenulls);
		return $res;
	}

	function GetInsertSQL(&$rs, $arrFields,$magicq=false,$forcenulls=null) {
		$res =& $this->db->GetInsertSQL($rs, $arrFields,$magicq,$forcenulls);
		return $res;
	}


	// Blob/Clob Handling

	function UpdateBlob($table,$column,$val,$where,$blobtype='BLOB') {
		$res =& $this->db->UpdateBlob($table,$column,$val,$where,$blobtype);
		return $res;
	}

	function UpdateClob($table,$column,$val,$where) {
		$res =& $this->db->UpdateClob($table,$column,$val,$where);
		return $res;
	}

	function UpdateBlobFile($table,$column,$path,$where,$blobtype='BLOB') {
		$res =& $this->db->UpdateBlobFile($table,$column,$path,$where,$blobtype);
		return $res;
	}

	function BlobEncode($blob) {
		$res =& $this->db->BlobEncode($blob);
		return $res;
	}

	function BlobDecode($blob) {
		$res =& $this->db->BlobDecode($blob);
		return $res;
	}


	// Paging/Scrolling

	function &PageExecute($sql, $nrows, $page, $inputarr=false, $secs2cache=0) {
		$rs =& $this->db->PageExecute($sql, $nrows, $page, $inputarr, $secs2cache);
		return $rs;
	}

	function &CachePageExecute($secs2cache, $sql, $nrows, $page,$inputarr=false) {
		$rs =& $this->db->CachePageExecute($secs2cache, $sql, $nrows, $page,$inputarr);
		return $rs;
	}


	// CleanUp

	function Close() {
		if($this->db != NULL) {
			$this->db->Close();
			$this->db = NULL;
			$this->driverConn = False;
		}
	}

	// See above
	//function CacheFlush($sql=false,$inputarr=false) {
	//	$res =& $this->db->CacheFlush($sql,$inputarr);
	//	return $res;
	//}


	// Transactions

	function BeginTrans() {
		$res =& $this->db->BeginTrans();
		return $res;
	}

	function CommitTrans($ok=true) { 
		$res =& $this->db->CommitTrans($ok);
		return $res;
	}

	function RollbackTrans() {
		$res =& $this->db->RollbackTrans();
		return $res;
	}

	function SetFetchMode($mode) {	
		$res =& $this->db->SetFetchMode($mode);
		return $res;
	}


	// String Manipulation

	function Concat() {
		$res =& $this->db->Concat();
		return $res;
	}

	function qstr($s,$magic_quotes=false) {
		$res =& $this->db->qstr($s,$magic_quotes);
		return $res;
	}

	function Quote($s) {
		$res =& $this->Quote($s);
		return $res;
	}


	// Date Handling



	// Row Management



	// Sequences



	// Error Handling




	// Data Dictionary



	// Statistics and Query Rewriting



	// Deprecated







	function ServerInfo($table) {
		return $this->db->ServerInfo($table);
	}

	function DBDate($dateStr) {
		return $this->db->DBDate($dateStr);
	}








	/**
	* Error message handling. Returns the last error message.
	* @returns  string
	*/
	function ErrorMsg() {
		$errorMsg =& $this->db->ErrorMsg();
		return $errorMsg;
	}



	/////
	// PEAR::DB Compatability methods

	/**
	* PEAR DB Compat - do not use internally.
	* @returns 	RecordSet
	*/
	function &Query($sql, $inputArray=False) {
		$rs =& $this->db->Execute($sql, $inputArray);
		return $rs;
	} 

	/**
	* PEAR DB Compat - do not use internally
	* @returns 	
	*/
	function Disconnect() {
		return $this->Close();
	}


}


/**
* Hacking ADOdb Safely.
*  <p>See: ADOdb Library for PHP Manual::Code Initialization and Connecting to 
*	Databases</p>
* 
*  <p>"You might want to modify ADOdb for your own purposes. Luckily you can still 
*  maintain backward compatibility by sub-classing ADOdb and using the 
*  $ADODB_NEWCONNECTION variable. $ADODB_NEWCONNECTION allows you to override the 
*  behaviour of ADONewConnection(). ADOConnection() checks for this variable and 
*  will call the function-name stored in this variable if it is defined."</p>
*/
#class hack_rs_mysql extends ADORecordSet_mysqlt {
#class hack_rs_mysqlt extends ADORecordSet_mysqlt {
#	/* record set modify place here*/
#}


// $ADODB_NEWCONNECTION allows you to override the behaviour of ADONewConnection()
#$ADODB_NEWCONNECTION = 'hack_factory';

#function& hack_factory() {
#	$obj = new AdodbDataSource();
#	return $obj;
#}

?>
