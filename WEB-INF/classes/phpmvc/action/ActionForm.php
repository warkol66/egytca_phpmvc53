<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/classes/phpmvc/action/ActionForm.php,v 1.4 2006/02/22 06:53:49 who Exp $
* $Revision: 1.4 $
* $Date: 2006/02/22 06:53:49 $
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
* <p>An <strong>ActionForm</strong> is a Bean optionally associated 
* with one or more <code>ActionMappings</code>. Such a bean will have had its
* properties initialized from the corresponding request parameters before
* the corresonding action's <code>perform()</code> method is called.</p>
*
* <p>When the properties of this bean have been populated, but before the
* <code>perform()</code> method of the action is called, this bean's
* <code>validate()</code> method will be called, which gives the bean a chance
* to verify that the properties submitted by the user are correct and valid.
* If this method finds problems, it returns an error messages object that
* encapsulates those problems, and the controller servlet will return control
* to the corresponding input form.  Otherwise, the <code>validate()</code>
* method returns <code>NULL()</code>, indicating that everything is acceptable
* and the corresponding Action's <code>perform()</code> method should be
* called.</p>
*
* <p>This <b>abstract</b> class must be subclassed in order to be instantiated.
* Eg: LogonForm. Subclasses should provide property getter and setter methods
* for all of the bean properties they wish to expose, plus override any of
* the public orprotected methods for which they wish to provide modified
* functionality.</p>
*
* @author: John C Wildenauer (php.MVC port)<br>
*  Credits:<br>
*  Craig R. McClanahan (Tomcat/catalina class: see jakarta.apache.org)
* @version $Revision: 1.4 $
* @public
*/
class ActionForm {

	// ----- Protected Instance Variables ----------------------------------- //

	/**
	* The controller servlet instance to which we are attached.
	* @private
	* @type ActionServer
	*/
	var $actionServer = NULL;	// $servlet


	/**
	* The MultipartRequestHandler for this form, can be <code>NULL</code>
	* @private
	* @type MultipartRequestHandler
	*/
	var $multipartRequestHandler = NULL;


	// ----- Properties --------------------------------------------------- //

	/**
	* Return the controller servlet instance to which we are attached.
	* @public
	* @returns ActionServer
	*/
	function getActionServer() {

		return $this->actionServer;

	}


	/**
	* Return the controller servlet instance to which we are attached.
	* Later !!!!!!!!!!!!
	* @public
	* @return ActionServletWrapper
	*/
	function getActionServerWrapper() {
	
		#return new ActionServletWrapper(getServlet());
		return NULL;	// Later !!!!!!!!!!!!
	
	}


	/**
	* Return the MultipartRequestHandler for this form
	* The reasoning behind this is to give form bean developers
	* control over the lifecycle of their multipart requests
	* through the use of the finish() and/or rollback() methods
	* of MultipartRequestHandler.  This method will return
	* <code>NULL</code> if this form's enctype is not
	* "multipart/request-data".
	*
	* @public
	* @returns MultipartRequestHandler
	* Xsee MultipartRequestHandler
	*/
	function getMultipartRequestHandler() {
		return $this->multipartRequestHandler;
	}


	/**
	* Set the controller servlet instance to which we are attached (if
	* <code>servlet</code> is non-null), or release any allocated resources
	* (if <code>servlet</code> is null).
	*
	* @param ActionServer, The new action controller server, if any
	* @public
	* @returns void
	*/
	function setActionServer(&$actionServer) {

		$this->actionServer =& $actionServer;

	}


	/**
	* Set the MultipartRequestHandler
	*
	* @param MultipartRequestHandler
	* @public
	* @returns void
	*/
	function setMultipartRequestHandler($multipartRequestHandler) {
		$this->multipartRequestHandler = $multipartRequestHandler;
	}


	// ----- Public Methods ------------------------------------------------- //

	/**
	* Reset all bean properties to their default state.  This method is
	* called before the properties are repopulated by the controller servlet.
	* <p>
	* The default implementation attempts to forward to the HTTP
	* version of this method.
	*
	* @param ActionMapping		The mapping used to select this instance
	* @param HttpRequestBase	The server request we are processing
	* @public
	* @returns void
	*/
	function reset($mapping, $request) {

		// SEE BELOW
		$this->resetHttp($mapping, $request); // (HttpServletRequest)

	}


	/**
	* Reset all bean properties to their default state.  This method is
	* called before the properties are repopulated by the controller servlet.
	* <p>
	* The default implementation does nothing.  Subclasses should override
	* this method to reset all bean properties to default values.
	*
	* @param ActionMapping		The mapping used to select this instance
	* @param HttpRequestBase	The servlet request we are processing
	* @public
	* @returns void
	*/
	function resetHttp($mapping, $request) {

		; // Default implementation does nothing

	}


	/**
	* Validate the properties that have been set for this non-HTTP request,
	* and return an <code>ActionErrors</code> object that encapsulates any
	* validation errors that have been found.  If no errors are found, return
	* <code>NULL</code> or an <code>ActionErrors</code> object with no
	* recorded error messages.
	* <p>
	* The default implementation attempts to forward to the HTTP version of
	* this method.
	*
	* @param ActionMapping		The mapping used to select this instance
	* @param HttpRequestBase	The server request we are processing
	* @public
	* @returns ActionErrors
	*/
	function validate($mapping, $request) {

		$validate = $this->validateHttp($mapping, $request); // (HttpServletRequest)

		if(! $validate) {
			return NULL;
		}

		return $validate;

	}


	/**
	* Validate the properties that have been set for this HTTP request,
	* and return an <code>ActionErrors</code> object that encapsulates any
	* validation errors that have been found.  If no errors are found,
	* return <code>null</code> or an <code>ActionErrors</code> object with
	* no recorded error messages.
	* <p>
	* The default ipmlementation performs no validation and returns
	* <code>null</code>.  Subclasses must override this method to provide
	* any validation they wish to perform.
	*
	* @param ActionMapping		The mapping used to select this instance
	* @param HttpRequestBase	The servlet request we are processing
	* @public
	* @returns ActionErrors
	*/
	function validateHttp($mapping, $request) {
	
        return NULL;	// Override this method in subclass to do validation

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
	* @private
	* @returns void 
	*/
	function saveErrors(&$request, $errors) {

		// Remove any error messages attribute if none are required
		if(($errors == NULL) || $errors->isEmpty()) {
			$request->removeAttribute(Action::getKey('ERROR_KEY'));
			return;
		}
		
		// Save the error messages we need
		$request->setAttribute(Action::getKey('ERROR_KEY'), $errors);

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
			$request->removeAttribute(Action::getKey('FORM_BEAN_KEY'));
			return;
		}

		// Save the form bean
		$request->setAttribute(Action::getKey('FORM_BEAN_KEY'), $form);

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
			$request->removeAttribute(Action::getKey('VALUE_OBJECT_KEY'));
			return;
		}
		
		// Save the value object
		$request->setAttribute(Action::getKey('VALUE_OBJECT_KEY'), $valueObject);

	}

}
?>