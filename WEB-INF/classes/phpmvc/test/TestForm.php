<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/classes/phpmvc/test/TestForm.php,v 1.4 2006/02/22 08:54:18 who Exp $
* $Revision: 1.4 $
* $Date: 2006/02/22 08:54:18 $
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
* TestForm bean
*
* @author John C Wildenauer (php.MVC port)
* @version $Revision: 1.4 $
* @public
*/
class TestForm extends ActionForm {

	// ----- Instance Variables --------------------------------------------- //

	// ----- Properties --------------------------------------------------- //

	// ----- Public Methods ------------------------------------------------- //

	/**
	* Reset all properties to their default values.
	*
	* @param ActionMapping		The mapping used to select this instance
	* @param HttpRequestBase	The servlet request we are processing
	*/
	function reset($mapping, $request) {


	}


	/**
	* Validate the properties that have been set from this HTTP request,
	* and return an <code>ActionErrors</code> object that encapsulates any
	* validation errors that have been found. If no errors are found, return
	* <code>NULL</code>
	*
	* @param ActionMapping		The mapping used to select this instance
	* @param HttpRequestBase	The servlet request we are processing
	* @returns ActionErrors
	*/
	function validate($mapping, $request) {

		#if($errors->isEmpty())
		#	return NULL;
		#else
		#	return $errors;
	}

}
?>