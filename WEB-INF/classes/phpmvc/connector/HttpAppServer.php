<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/classes/phpmvc/connector/HttpAppServer.php,v 1.4 2006/02/22 08:15:03 who Exp $
* $Revision: 1.4 $
* $Date: 2006/02/22 08:15:03 $
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
* HttpAppServer.php
* Ref: Class javax.servlet.http.HttpServlet
*
* <p>Provides an framework for handling the HTTP protocol. Because it is an 
* abstract class, servlet writers must subclass it and override at least 
* one method. The methods normally overridden are:<br>
* <code>
* doGet ...<br>
* doPost ...<br>
* getServletInfo, provide descriptive information ...
* </code>
* 
* @author John C. Wildenauer
* @version $Revision: 1.4 $
* @public
*/
class HttpAppServer {

	// ----- Instance Variables --------------------------------------------- //

	/**
	* Commons Logging instance.
	* @private
	* @type Log
	*/
	var $log = NULL;
	

	// ----- Constructors --------------------------------------------------- //

	function HttpAppServer() {

		$this->log	= new PhpMVC_Log();
		$this->log->setLog('isDebugEnabled'	, False);
		$this->log->setLog('isInfoEnabled'	, False);
		$this->log->setLog('isTraceEnabled'	, False);

		// ..........

	}


	// ----- Public Methods ------------------------------------------------- //



	// ----- Protected Methods --------------------------------------------- //

	/**
	* Performs the HTTP GET operation; the default implementation reports an 
	* HTTP BAD_REQUEST error.
	*
	* @param HttpRequestBase	The http request specified by the caller
	* @param HttpResponseBase	The http request specified by the caller
	* @public
	* @returns void
	*/
	function doGet($request, $response) {
		// override
	}


	/**
	* Performs the HTTP POST operation; the default implementation reports an 
	* HTTP BAD_REQUEST error.
	*
	* @param HttpRequestBase	The http request specified by the caller
	* @param HttpResponseBase	The http request specified by the caller
	* @public
	* @returns void
	*/
	function doPost($request, $response) {
		// override
	}


	/**
	* This is an HTTP-specific version of the service method, which
	* accepts HTTP specific parameters.
	*
	* @param HttpRequestBase	The http request specified by the caller
	* @param HttpResponseBase	The http request specified by the caller
	* @private
	* @returns void
	*/
	function service($request, $response) {

		$debug = $this->log->getLog('isDebugEnabled');
		$trace = $this->log->getLog('isTraceEnabled');

		if($trace)
			$this->log->trace('Start: HttpAppServer->service(..)');

	}

}
?>