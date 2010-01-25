<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/classes/phpmvc/plugins/SmartyPlugInDriver.php,v 1.2 2006/02/22 08:50:59 who Exp $
* $Revision: 1.2 $
* $Date: 2006/02/22 08:50:59 $
*
* ====================================================================
*
* License:	GNU Lesser General Public License (LGPL)
*
* Copyright (c) 2003-2006 John C.Wildenauer.  All rights reserved.
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
* SmartyPlugInDriver is a concrete implementation of the abstract APlugIn class.
*
* <p>This class is a wrapper class that implements the Smarty compiling PHP
* template engine.</p>
*
* @author John C. Wildenauer
* @version $Revision: 1.2 $
*/
class SmartyPlugInDriver extends APlugIn {

	// ----- Properties ----------------------------------------------------- //

	// See abstract properties


	// ----- Constructor ---------------------------------------------------- //

	/**
	* Implement the Smarty compiling PHP template engine.
	*
	* @public
	* @returns void
	*/
	function SmartyPlugInDriver() {

		// Build the parent first
		parent::APlugIn();
	
		// Using Smarty class
		$this->plugIn =& new Smarty;

	}


	// ----- Public Methods ------------------------------------------------- //

	// See abstract mathods

}
?>