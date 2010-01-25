<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/classes/phpmvc/utils/HelperUtils.php,v 1.2 2006/02/22 08:14:27 who Exp $
* $Revision: 1.2 $
* $Date: 2006/02/22 08:14:27 $
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
* General helper utility methods
* 
* @author John C. Wildenauer
* @version $Revision: 1.2 $
* @public
*/
class HelperUtils {

	/**
	* Remove an element from an array, including the key.<br>
	*
	* Returns <code>True</code> if the key was found in the array , or 
	* <code>False</code> if the key is not found in the array.<br>
	* (Concept contributed by: mschmitz - Michael)<br>
	
	* Usage: HelperUtils::zapArrayElement('key', $myArray);
	*
	* @param string	The array element key
	* @param array		The array (reference) containing the element to be removed
	* @public
	* @returns array
	*/
	function zapArrayElement($key, &$array) {

		$idx = array_search( $key, array_keys($array) );

		if( $idx === NULL || $idx === False) {
			return False; // Not found, nothing to do
		} else {
			array_splice( $array, $idx, 1);
			return True;
		}

	}



}
?>