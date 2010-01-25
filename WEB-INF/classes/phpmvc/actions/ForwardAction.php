<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/classes/phpmvc/actions/ForwardAction.php,v 1.4 2006/02/22 07:30:19 who Exp $
* $Revision: 1.4 $
* $Date: 2006/02/22 07:30:19 $
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
* A php.MVC framework class we can use when we don't require any
* business logic processing. This class provides a generic execute() method.
*
* <p>This will forward control to the context-relative URI specified by the
* 'parameter' attribute of the <action ... parameter = "myPage.php">.
* <p>Program execution will terminate in RequestProcessor->process(...)
*
* <p>Note: A regular Action class would return a Forward object and
* continue program execution.
*
* <p>To configure the use of this Action in your
* <code>struts-config.xml</code> file, create an entry like this:</p>
*
* <pre>
*   &lt;action path="saveSubscription"
*           type="ForwardAction"
*           name="subscriptionForm"
*          scope="request"
*       validate="true"
*      parameter="regUsersMenu.tpl"&gt;
* </pre>
*
* <p>which will forward control to the context-relative URI specified by the
* <code>parameter</code> attribute.</p>
*
* @author John C Wildenauer (php.MVC port)<br>
*  Credits:<br>
*  Craig R. McClanahan (original Jakarta Struts class)
* @version $Revision: 1.4 $
* @public
*/
class ForwardAction extends Action {

	// ----- Protected Instance Variables ----------------------------------- //

	/**
	* The message resources for this package.
	* @private
	* @type MessageResources
	*/
	#var $messages = NULL; // MessageResources "actions.LocalStrings";

	/**
	* Commons Logging instance.
	* @private
	* @type Log
	*/
	var $log = NULL;


	// ----- Constructors --------------------------------------------------- //

	function ForwardAction() {

		$this->log	= new PhpMVC_Log();
		$this->log->setLog('isDebugEnabled'	, False);
		$this->log->setLog('isInfoEnabled'	, False);
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
	* Note: This method was named perform(). perform() is depreciated
	*        and replaced with execute()
	*
	* @param ActionMapping		The ActionMapping used to select this instance
	* @param ActionForm			Optional ActionForm bean for this request, if any
	* @param HttpRequestBase	The HTTP request we are processing
	* @param HttpResonseBase	The HTTP response we are creating
	* @public
	* @returns ActionForward
	*/
	function execute($mapping, $form, $request, $response) {

		$debug = $this->log->getLog('isDebugEnabled');
		$trace = $this->log->getLog('isTraceEnabled');

		if($trace) {
			$this->log->trace('Start: ForwardAction->execute(...)'.
									'['.__LINE__.']');
		}

		// Create a RequestDispatcher the corresponding resource
		$path = $mapping->getParameter();	// String. The URI
		if($path == NULL) {
			#response.sendError(...));
			return NULL;
		}

		$appServerContext = 
				$this->actionServer->appServerConfig->getAppServerContext();
		$actionDispatcher = $appServerContext->getInitParameter('ACTION_DISPATCHER');		
		$ad = new $actionDispatcher; // RequestDispatcher for the application

		if($ad == NULL) {
			#response.sendError(...);
			return;
		}

		// Set a reference to the ActionServer instance
		$ad->setActionServer($this->actionServer);

		// Forward control to the specified resource
		// Eg: We now call the ActionDispatcher to output a resource.
		// Something like 'myPage.php'
		$ad->forward($path, $request, $response);

		// Tell the controller servlet that the response has been created
		return NULL;

	}
}
?>