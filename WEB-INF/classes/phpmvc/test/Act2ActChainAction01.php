<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/classes/phpmvc/test/Act2ActChainAction01.php,v 1.4 2006/02/22 06:30:07 who Exp $
* $Revision: 1.4 $
* $Date: 2006/02/22 06:30:07 $
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
* Test Action chaining for a regular <code>Action</code>.<br>
* Eg: Action -> Static resource (Page)
*
* @author John C Wildenauer (php.MVC port)
* @version
* @access public
*/
class Act2ActChainAction01 extends Action {

	// ----- Instance Variables --------------------------------------------- //

	/**
	* The <code>Log</code> instance for this application.
	* @access private
	* @type Log
	*/
	var $log = NULL;


	// ----- Constructor ---------------------------------------------------- //

	function Act2ActChainAction01() {
		
		$this->log	= new PhpMVC_Log();
		$this->log->setLog('isDebugEnabled'	, False);
		$this->log->setLog('isTraceEnabled'	, False);

	}


	// ----- Public Methods ------------------------------------------------- //

	/**
	* Process the specified HTTP request, and create the corresponding HTTP
	* response (or forward to another web component that will create it).
	* Return an <code>ActionForward</code> instance describing where and how
	* control should be forwarded, or <code>null</code> if the response has
	* already been completed.
	*
	* @param mapping ActionConfig, The ActionConfig (mapping) used to select 
	*   this instance
	* @param form ActionForm, The optional ActionForm bean for this request (if any)
	* @param request HttpServletRequest, The HTTP request we are processing
	* @param response HttpServletResponse, The HTTP response we are creating
	*
	* @access public
	* @return ActionForward
	* #@exception Exception if business logic throws an exception
	*/
	function execute($mapping, $form, &$request, &$response) {

		$debug = $this->log->getLog('isDebugEnabled');
		$trace = $this->log->getLog('isTraceEnabled');

		if($trace) {
			$this->log->trace('Start: Act2ActChainAction01->execute(...)'.
									'['.__LINE__.']');
		}

		// Forward control to the specified success URI
		// <action .../> <forward name ="nextActionPath" .../> </action>
		return $mapping->findForwardConfig('nextActionPath');

	}

}
?>