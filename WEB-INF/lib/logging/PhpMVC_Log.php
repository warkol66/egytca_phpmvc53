<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/lib/logging/PhpMVC_Log.php,v 1.2 2006/02/22 08:28:17 who Exp $
* $Revision: 1.2 $
* $Date: 2006/02/22 08:28:17 $
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
* A simple light-weight logging class
*
* @author John C. Wildenauer
* @version $Revision: 1.2 $
* @public
*/
class PhpMVC_Log {

	var $attribute	= array();
	
	var $logPtr		= NULL;	// log file reference pointer

	function PhpMVC_Log($key='', $val='') {	
		if($key != '' && $val != '') {
			$this->setLog($key, $val);
			
			#$this->setLog('isTraceEnabled', True);
			#$this->setLog('isDebugEnabled', True);
			#$this->setLog('isErrorEnabled', True);
		}
	}

	function setLog($key, $val) {
		$this->attribute[$key] = $val;
	}

	function getLog($key) {
		if( array_key_exists($key, $this->attribute) ) 
			return $this->attribute[$key];
		else
			return NULL;	
	}

	function clearLog() {
		unset($this->attribute);
		$this->attribute = array();
	}

	function debug($msgString) {
		echo 'Debug: '.$msgString."\n";	
	}

	function warn($msgString) {
		echo 'Warning: '.$msgString."\n";	
	}

	function error($msgString, $error='') {
		echo 'Error: '.$msgString.' ('.$error.")\n";	
	}

	function trace($msgString) {
		echo 'Trace: '.$msgString."\n";	
	}

	function write($msgString, $logFile='log.txt', $mode='w') {

		if($logFile != '' && $this->logPtr == NULL) {
			$this->logPtr = @fopen($logFile, $mode);
		}

		if($this->logPtr != NULL) {
			fwrite($this->logPtr, $msgString);
		} else {
			echo 'Error writing log file: ['.$logFile.']<br>';
		}
		
	}

	function closeLogFile() {
		
		if($this->logPtr != NULL) {
			fclose($this->logPtr);
		}
	}


}
?>