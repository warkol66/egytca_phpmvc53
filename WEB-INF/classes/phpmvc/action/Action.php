<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/classes/phpmvc/action/Action.php,v 1.5 2006/02/22 06:34:27 who Exp $
* $Revision: 1.5 $
* $Date: 2006/02/22 06:34:27 $
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
* An <strong>Action</strong> is an adapter between the contents of an incoming
* HTTP request and the corresponding business logic that should be executed to
* process this request. The controller (ActionServer) will select an
* appropriate Action for each request, create an instance (if necessary),
* and call the <code>perform</code> method.</p>
*
* <p>When an <code>Action</code> instance is first created, the ActionServer
* controller will call <code>$this->setActionServer()</code> with an ActionServer
* parameter referencing the ActionServer controller instance to which this 
* Action is attached.
*
* When the ActionServer controller is to be shut down (or restarted), the
* <code>$this->setActionServer()</code> method will be called with a 
* <code>NULL</code> argument, which can be used to clean up any allocated 
* resources in use by this Action.</p>
*
* @author John C Wildenauer (php.MVC port)<br>
*  Credits:<br>
*  Craig R. McClanahan (Tomcat/catalina class: see jakarta.apache.org)
* @version $Revision: 1.5 $
* @public
*/
class Action {

	// ----- Instance Variables --------------------------------------------- //

	/**
	* The system default Locale. [protected static]
	* @private
	* @type Locale
	*/
	var $defaultLocale = NULL; // Locale.getDefault()

	/**
	* The ActionServer controller to which we are attached. [protected]
	* @private
	* @type ActionServer
	*/
	var $actionServer = NULL;


	// ----- Manifest Constants --------------------------------------------- //

	/**
	* The context attributes key under which our <code>ActionServer</code>
	* instance will be stored.
	*
	* @param string
	* @public	
	* @returns string
	*/
	function getKey($key) {

		// Simulate Java static final constants - Better way !!!
		switch($key) {
			case 'ACTION_SERVER_KEY': 
				$keyVal = 'phpmvc.action.ACTION_SERVER';
				break;
			case 'APPLICATION_KEY': 
				$keyVal = 'phpmvc.action.APPLICATION';
				break;
			case 'DATA_SOURCE_KEY': 
				$keyVal = 'phpmvc.action.DATA_SOURCE';
				break;
			case 'ERROR_KEY': 
				$keyVal = 'phpmvc.action.ERROR';
				break;	
			case 'EXCEPTION_KEY': 
				$keyVal = 'phpmvc.action.EXCEPTION';
				break;
			case 'LOCALE_KEY': 
				$keyVal = 'phpmvc.action.LOCALE';
				break;
			case 'MAPPING_KEY': 
				$keyVal = 'phpmvc.action.mapping.instance';
				break;
			case 'MESSAGE_KEY': 
				$keyVal = 'phpmvc.action.ACTION_MESSAGE';
				break;
			case 'MESSAGES_KEY': 
				$keyVal = 'phpmvc.action.MESSAGES';
				break;
			case 'MULTIPART_KEY': 
				$keyVal = 'phpmvc.action.mapping.multipartclass';
				break;
			case 'APP_SERVER_KEY': 
				$keyVal = 'phpmvc.action.APP_SERVER_MAPPING';
				break;
			case 'TRANSACTION_TOKEN_KEY': 
				$keyVal = 'phpmvc.action.TOKEN';
				break;
			case 'FORM_BEAN_KEY': 
				$keyVal = 'phpmvc.action.FORM_BEAN';
				break;
			case 'VALUE_OBJECT_KEY': 								// Business data object
				$keyVal = 'phpmvc.action.VALUE_OBJECT';
				break;
			default:
				$keyVal = 'phpmvc.action.ERROR_KEY_NOT_FOUND';

		}
		
		return $keyVal;
	}


	// ----- Properties ----------------------------------------------------- //

	/**
	* Return the ActionServer controller instance to which we are attached.
	* @public
	* @returns ActionServer
	*/
	function &getActionServer() {

		return $this->actionServer;

	}


	/**
	* Set the ActionServer controller instance to which we are attached (if
	* <code>actionServer</code> is non-null), or release any allocated resources
	* (if <code>actionServer</code> is NULL).
	*
	* @param ActionServer The new ActionServer controller, if any
	* @public
	* @returns void
	*/
	function setActionServer(&$actionServer) {

		$this->actionServer =& $actionServer;

	}


	// ----- Public Methods ------------------------------------------------- //

	// .........................................................

	/**
	* Process the specified HTTP request, and create the corresponding HTTP
	* response (or forward to another web component that will create it),
	* with provision for handling exceptions thrown by the business logic.
	*
	* Override this method in an extended Action class (eg: LoginAction) to
	* define business class logic. This method is called from RequestProcessor
	* to process an HTTP or HTTPS request.
	*
	* @param ActionMapping The ActionMapping used to select this instance
	* @param ActionForm The optional ActionForm bean for this request (if any)
	* @param ServletRequest The non-HTTP request we are processing
	* @param ServletResponse The non-HTTP response we are creating
	* @public
	* @returns ActionForward
	*/
	function execute($mapping, $form, &$request, &$response) {

		return NULL;  // Override this method to provide functionality 

    }


	// ----- Protected Methods ---------------------------------------------- //

	/**
	* Save the specified error messages keys into the appropriate request
	* attribute for use by the &lt;phpmvc:errors&gt; handler, if any messages
	* are required. Otherwise, ensure that the request attribute is not
	* created.
	*
	* @param HttpServletRequest The servlet request we are processing
	* @param ActionErrors Error messages object
	* @privare
	* @returns void 
	*/
	function saveErrors(&$request, $errors) {

		// Remove any error messages attribute if none are required
		if(($errors == NULL) || $errors->isEmpty()) {
			$request->removeAttribute($this->getKey('ERROR_KEY'));
			return;
		}
		
		// Save the error messages we need
		$request->setAttribute($this->getKey('ERROR_KEY'), $errors);

	}


	/**
	* Save the specified FormBean object into the appropriate request
	* attribute for use by the template resource, if a FormBean object
	* as required. Otherwise, ensure that the request attribute is not
	* created.
	*
	* @param HttpServletRequest	The servlet request we are processing
	* @param								ActionForm The ActionForm object
	* @private
	* @returns void 
	*/
	function saveFormBean(&$request, &$form) {

		// Remove any form bean attribute if none are required
		if(($form == NULL)) {
			$request->removeAttribute($this->getKey('FORM_BEAN_KEY'));
			return;
		}

		// Save the form bean
		$request->setAttribute($this->getKey('FORM_BEAN_KEY'), $form);

	}


	/**
	* Save the specified ValueObject (business data) object into the appropriate
	*  request attribute for use by the template resource, if a ValueObject object
	* as required. Otherwise, ensure that the request attribute is not
	* created.
	*
	* @param HttpServletRequest	The servlet request we are processing
	* @param Object					The ValueObject (business data) object
	* @private
	* @returns void 
	*/
	function saveValueObject(&$request, &$valueObject) {

		// Remove any value object attribute if none are required
		if(($valueObject == NULL)) {
			$request->removeAttribute($this->getKey('VALUE_OBJECT_KEY'));
			return;
		}
		
		// Save the value object
		$request->setAttribute($this->getKey('VALUE_OBJECT_KEY'), $valueObject);

	}

	// .........................................................

}
?>