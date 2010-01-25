<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/lib/collections/HashMap.php,v 1.3 2006/02/22 08:13:43 who Exp $
* $Revision: 1.3 $
* $Date: 2006/02/22 08:13:43 $
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
* HashMap
*
* @author John C. Wildenauer
* @version $Revision: 1.3 $
* @public
*/
class HashMap {

	var $stack = array();

	function HashMap($key='', $val='') {	
		if($key != '' && $val != '') {
			$this->put($key, $val);
		}
	}

	function put($key, $val) {
		$this->stack[$key] = $val;
	}


	function peek($key) {
		return $this->stack[$key];	
	}

	// Alias for peek()
	function getValue($key) {
		return $this->peek($key);	
	}

	function getArrayList($key) {
		$arrayList = NULL;	// array();
		foreach($this->stack as $k => $v) {
			if($k == $key) {
				$arrayList[] = $v; // Note: Illegal to use an object ref as key
			}
		}

		return $arrayList;	
	}


	// A set view of the keys contained in this map
	function getKeySet() {
		$arrayList = array();
		foreach($this->stack as $k => $v) {
			$arrayList[$k] = NULL;
		}

		return $arrayList;	
	}

	function clear() {
		unset($this->stack);
		$this->stack = array();
	}

	function stringArrayList() {
		$sb=NULL;
		$sb .= "***** ------------------------------------------- *****<br>\n";
		foreach($this->stack as $k => $v) {
			$sb .= "Key: ".$k." Value: ".$v."<br>\n";
		}
		$sb .= "***** ------------------------------------------- *****<br>\n";
		return $sb;
	}

}
?>