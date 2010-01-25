<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/classes/phpmvc/utils/ActionDispatcher.php,v 1.8 2006/02/22 06:50:44 who Exp $
* $Revision: 1.8 $
* $Date: 2006/02/22 06:50:44 $
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
* Standard implementation of <code>RequestDispatcher</code> that allows a
* request to be forwarded to a different resource to create the ultimate
* response, or to include the output of another resource in the response
* from this resource.
* Refer: apache/catalina/core/ApplicationDispatcher.java
*
* @author John C Wildenauer (php.MVC port)<br>
*  Credits:<br>
*  Craig R. McClanahan (Tomcat/catalina class: see jakarta.apache.org)
* @version $Revision: 1.8 $
* @public
*/
class ActionDispatcher {

	// ----- Instance Variables --------------------------------------------- //

	/**
	* The request specified by the dispatching application.
	* @private
	* @type HttpRequestBase
	*/
	var $appRequest = NULL;


	/**
	* The response specified by the dispatching application.
	* @private
	* @type HttpResponseBase
	*/
	var $appResponse = NULL;


	/**
	* The Context this RequestDispatcher is associated with.
	* @private
	* @type Context
	*/
	var $context = NULL;


	/**
	* The debugging detail level for this component.
	* @private
	* @type int
	*/
	var $debug = 0;


	/**
	* Are we performing an include() instead of a forward()?
	* @private
	* @type boolean
	*/
	var $including = False;


	/**
	* Descriptive information about this implementation.
	* @private
	* @type string
	*/
	var $info = 'phpmvc.ApplicationDispatcher/1.0';


	/**
	* The AppServer name for a named dispatcher.
	* @private
	* @type string
	*/
	var $name = NULL;


	/**
	* The outermost request that will be passed on to the invoked servlet.
	* @private
	* @type HttpRequestBaset
	*/
	#var $outerRequest = NULL;


	/**
	* The outermost response that will be passed on to the invoked servlet.
	* @private
	* @type HttpResponseBase
	*/
	#var $outerResponse = NULL;


	/**
	* The extra path information for this RequestDispatcher.
	* @private
	* @type string
	*/
	var $pathInfo = NULL;


	/**
	* The query string parameters for this RequestDispatcher.
	* @private
	* @type string
	*/
	var $queryString = NULL;


	/**
	* The AppServer path for this RequestDispatcher.
	* @private
	* @type string
	*/
	var $appServerPath = NULL;	// servletPath


	/**
	* The StringManager for this package.
	* @private
	* @type StringManager
	*/
	#var $sm = StringManager.getManager(Constants.Package);


	/**
	* The Wrapper associated with the resource that will be forwarded to
	* or included.
	*
	* <p>JCW (See: org.apache.catalina Interface  Wrapper)
	* A Wrapper is a Container that represents an individual servlet
	* definition from the deployment descriptor of the web application. It
	* provides a convenient mechanism to use Interceptors that see every single
	* request to the servlet represented by this definition
	*
	* @private
	* @type Wrapper
	*/
	#var $wrapper = NULL;


	/**
	* The request wrapper we have created and installed (if any).
	* @private
	* @type HttpRequestBase
	*/
	#var $wrapRequest = NULL;


	/**
	* The response wrapper we have created and installed (if any).
	* @private
	* @type HttpResponseBase
	*/
	#var $wrapResponse = NULL;


	/** JCW
	* Commons Logging instance.
	* @private
	* @type Log
	*/
	var $log = NULL;


	/**
	* Uri or Definition name to forward (Eg: '/index.php').
	* @private
	* @type string
	*/
	var $uri = NULL;


	/**
	* An ActionServer reference
	* @type ActionServer
	* @private
	*/
	var $actionServer = NULL;


	// ----- Properties ----------------------------------------------------- //

	/**
	* Return the descriptive information about this implementation.
	*
	* @public
	* @returns string
	*/
	function getInfo() {

		return $this->info;

	}

	/**
	* Return an ActionServer reference
	*
	* @public
	* @returns ActionServer
	*/
	function &getActionServer() {

		return $this->actionServer;

	}

	/**
	* Set an ActionServer reference
	*
	* @public
	* @returns void
	*/
	function setActionServer(&$actionServer) {

		$this->actionServer =& $actionServer;

	}


	// ----- Constructors --------------------------------------------------- //

	/**
	* Construct a new instance of this class, configured according to the
	* specified parameters.  If both servletPath and pathInfo are
	* <code>null</code>, it will be assumed that this RequestDispatcher
	* was acquired by name, rather than by path.
	*
	* @param string	The Uri or Definition name to forward (Eg: '/index.php') [JCW]
	* @param  Wrapper	The Wrapper associated with the resource that will
	*  be forwarded to or included (required)
	* @param string	The revised servlet path to this resource (if any)
	* @param string	The revised extra path information to this resource
	*  (if any)
	* @param string	The query string parameters included with this request
	*  (if any)
	* @param string	The AppServer name (if a named dispatcher was created)
	*  else <code>null</code>
	*
	* @public
	* @returns
	*/
	function ActionDispatcher($uri='', $wrapper='', $servletPath='', $pathInfo='', $queryString='', $name='') {


		$this->log	= new PhpMVC_Log(); // see also:
		$this->log->setLog('isDebugEnabled'	, False);
		$this->log->setLog('isInfoEnabled'	, False);
		$this->log->setLog('isTraceEnabled'	, False);

		// Save all of our configuration parameters
		$this->uri			= $uri;
		$this->wrapper		= $wrapper;
		#$this->context	= $wrapper->getParent(); // (Context)
		$this->servletPath= $servletPath;
		$this->pathInfo	= $pathInfo;
		$this->queryString= $queryString;
		$this->name			= $name;


		$debug = $this->log->getLog('isDebugEnabled');
		if($debug) {
			$this->log->debug('ActionDispatcher::Constructor('.
								'uri='.$this->uri.
								', servletPath='.$this->servletPath.
								', pathInfo='.$this->pathInfo.
								', queryString='.$this->queryString.
                			', name='.$this->name.')');
		}

	}


	// ----- Public Methods ------------------------------------------------- //

	/**
	* Forward this request and response to another resource for processing.
	* Any runtime exception, IOException, or ServletException thrown by the
	* called servlet will be propogated to the caller.
	*
	* @param string				URI (Path) or Definition name to forward 
	*                          (Eg: '/index.php')
	* @param HttpRequestBase	The server request to be forwarded
	* @param HttpResponseBase	The server response to be forwarded
	*
	* @public
	* @returns void
	*/
	function forward($uri, &$request, &$response) {

		$debug = $this->log->getLog('isDebugEnabled');
		$trace = $this->log->getLog('isTraceEnabled');

		if($trace)
			$this->log->trace('Start: ActionDispatcher->forward(..)['.__LINE__.']');

		// Save all of our configuration parameters
		$this->uri = $uri;

		// Reset any output that has been buffered, but keep headers/cookies
		if($response->isCommitted()) {

			if($debug) {	// (debug >= 1)
				$this->log->debug('  Forward on committed response --> '.
										'IllegalStateException');

				#throw new IllegalStateException
				return 'IllegalStateException';
		}

		$e = $response->resetBuffer();	// bufferCount = 0

		// Catch (IllegalStateException e)
		if($e != NULL) {
			if($debug)
				$this->log->debug('  Forward resetBuffer()'.
										' returned IllegalStateException: '.
										$e);
				return $e; // throw e
			}
		}

		// Set up to handle the specified request and response
		$this->setup($request, $response, False);	// include() = False

		// Identify the HTTP-specific request and response objects (if any)
		$hrequest = NULL; // HttpServletRequest

		if( is_subclass_of($request, 'HttpRequestBase') )	// HttpServletRequest
			$hrequest = $request; // (HttpServletRequest)

		$hresponse = NULL; // HttpServletResponse
		if( is_subclass_of($response, 'HttpResponseBase') ) { // HttpServletResponse
			$hresponse = $response; // (HttpServletResponse)
		}

		// Handle a non-HTTP forward by passing the existing request/response
		if(($hrequest == NULL) || ($hresponse == NULL)) {
			if($debug)
				$this->log->debug(' Non-HTTP Forward');

			$this->invoke($request, $response);

		// Handle an HTTP named dispatcher forward
		} elseif( ($this->$appServerPath == NULL) && ($this->pathInfo == NULL) ) {

			if($debug)
				$this->log->debug(' Named Dispatcher Forward');

         $this->invoke($request, $response);

		// Handle an HTTP path-based forward
		} else { 

			if(debug)
				$this->log->debug(' HTTP Path Based Forward');

			$this->invoke($outerRequest, $response);
			#unwrapRequest();

		}

		// Commit and close the response before we return
		if($debug)
			$this->log->debug(' Committing and closing response');

		// Retrieve the output response contents
		$respBuff = $response->getResponseBuffer();

		// Check output mode. browser/smtp
		$mode = '';	// LATER
		if($mode == 'smtp') {

			// $eb = new EmailPageBuilder;
			// ...
			// $status = $eb->smtpEmailReportPage($responseBuff, ..);

		} else {

			// Send Headers/Cookies
			;

			// Send body contents
			echo $respBuff;

		}

    }


	// ----- Private Methods ------------------------------------------------ //

	/**
	* Ask the resource represented by this RequestDispatcher to process
	* the associated request, and create (or append to) the associated
	* response.
	* <p>
	*
	* @param HttpRequestBase	The servlet request we are processing
	* @param HttpResponseBase	The servlet response we are creating
	*
	* @private
	* @returns void
	*/
	function invoke(&$request, &$response) {

		$debug = $this->log->getLog('isDebugEnabled');
		$trace = $this->log->getLog('isTraceEnabled');

		if($trace)
			$this->log->trace('Start: ActionDispatcher->invoke(..)['.__LINE__.']');

		$servlet				= NULL; // Servlet servlet
		$ioException		= NULL; // IOException
		$servletException	= NULL; // ServletException
		$runtimeException	= NULL; // RuntimeException
		#$unavailable		= False;// boolean

	//<<<<<<<<<<<<<<<<<<< >>>>>>>>>>>>>>>>>>>>

		if( (is_subclass_of($request, 'HttpRequestBase')) &&
			(is_subclass_of($response, 'HttpResponseBase')) ) {

			if($debug)
				$this->log->debug('  Calling HTTP-specific $this->serviceResponse() ('.
										$this->uri.')');

			// HTTP-specific version of the Servlet.service method
			// Process request / return a Web page to the client
				
			// Build and dispatch the response (http/xml/smtp/...)
			$this->serviceResponse($request, $response);

		} else {

			if($debug)
				$this->log->debug('  Calling Non-HTTP-specific $this->serviceResponse() ('
										.$this->uri.')');

			// Build and dispatch the response (http/xml/smtp/...)
			$this->serviceResponse($request, $response);

		}

	}


	/**
	* The <code>ActionDispatcher->serviceResponse()</code> method provides basic View 
	* resource (template) handling:
	*
	* <p>The resource (template) to be displayed is retrieved according to the
	* ActionMapping <code>forward</code> path or <code>input</code> path defined
	* earlier in the request processing (and as declared in an <code>
	* &lt;action .../action&gt;</code> element of the application <code>
	* phpmvc-config.xml</code> configuration file).
	* </p>
	* <p>The <code>ActionDispatcher->serviceResponse()</code> method exposes three
	* objects to the View resources (templates):
	* <li>$form - The FormBean object</li>
	* <li>$errors - The Errors collection object</li>
	* <li>$data - The Value (business data) collection object</li>
	* <br>
	* If these objects have been previously created and saved to the <code>
	* request</code>, the object will now be available within the scope of the resource
	* template, otherwise the object will be set to <code>NULL</code>. 
	* </p>
	* <p>
	* For example some <b>Value</b> objects (business data) could be created within
	* an Action class and saved like this: 
	* <pre>
	*   $valueBeans =& new ValueBeans();
	*   $products[] = new Product('Gadget', 1025.00);
	*   $products[] = new Product('Widget', 52.65);
	*   $valueBeans->addValueBean('PRODUCTS_ARRAY', $products);
	*   $staff =& new Staff('Bruce', 'Sales', 'Karate');
	*   $valueBeans->addValueBean('STAFF', $staff);
	*   $this->saveValueObject($request, $valueBeans);
	* </pre>
	*
	* The Value object will now be accessable from within the template page scope,
	* something like:
	* <pre>
	*   &lt;?php
	*   $products = $data->getValueBean("PRODUCTS_ARRAY");
	*   $salesStaff = $data->getValueBean("STAFF");
	*   ?&gt;
	* </pre>
	* </p>
	* <p>The <code><b>FormBean</b></code> and <code><b>Errors</b></code> objects can
	* be saves from within an Action class in a similar way:
	* 
	* <pre>
	*   $this->saveFormBean($request, $form)
	*
	*   $this->saveErrors($request, $errors);
	* </pre>
	* And will be now available within the template page scope.
	* </p>
	* <p>Finally the requested View resource (template) is retrieved (to the $pageBuff)
	* and the resulting page contents is attached to the response buffer for dispatch 
	* to the client.		
	* </p>
	*
	* @param HttpRequestBase	The server request we are processing
	* @param HttpResponseBase	The server response we are creating
	*
	* @author	John Wildenauer	
	* @private
	* @returns void
	*/
	function serviceResponse(&$request, &$response) {

		$debug = $this->log->getLog('isDebugEnabled');
		$trace = $this->log->getLog('isTraceEnabled');

		if($trace)
			$this->log->trace('Start: ActionDispatcher->serviceResponse(..)['.__LINE__.']');

		// The resource (page) to display
		$resourceURI = $this->uri;

		// Remove any leading slashes from the URI
		$firstChar = substr($resourceURI, 0, 1);
		// Note the escaped "\"
		if($firstChar == '/' || $firstChar == '\\') {
			$resourceURI = substr($resourceURI, 1);
		}


		/////
		// Retrieve attributes from the Request object.
		// Note: $request->getAttribute(...) returns NULL if no value is set

		// Get our FormBean object
		$form		= $request->getAttribute(Action::getKey('FORM_BEAN_KEY'));

		// Get our Errors object
		$errors	= $request->getAttribute( Action::getKey('ERROR_KEY') );

		// Get our Value object (Business data)
		$data		= $request->getAttribute( Action::getKey('VALUE_OBJECT_KEY') );


		//////////
		// Get the resource configuration object (class ViewResourcesConfig)
		
		// Get the parent application configuration object: ApplicationConfig
		$appConfig = NULL;
		$appConfig = $request->getAttribute(Action::getKey('APPLICATION_KEY'));
		// Get the ViewResources configuration object: ViewResourcesConfig
		// Note: If the ViewResources instance has not been configured via an
		//       phpmvc-config.xml configuration file, a new instance is created
		//       when we call ApplicationConfig->getViewResourcesConfig() using
		//       the default ViewResourcesConfig class attributes.
		$viewConfig = NULL;
		$viewConfig =& $appConfig->getViewResourcesConfig();


		// Retrieve the requested page, to the $pageBuff
		$pageBuff = '';
		ob_start();
			include $resourceURI;
			$pageBuff = ob_get_contents();
		ob_end_clean();

		// Attach the output to the response object for later transmission
		$response->setResponseBuffer($pageBuff);

	}


	/**
	* Set up to handle the specified request and response
	*
	* @param HttpRequestBase	The servlet request specified by the caller
	* @param HttpResponseBase	The servlet response specified by the caller
	* @param boolean				Are we performing an include() as opposed to
	*  								a forward()?
	*
	* @private
	* @returns void
	*/
	function setup($request, $response, $including) {

		$this->appRequest		= $request;
		$this->appResponse	= $response;
		$this->including		= $including;

	}


	/**
	* Abstract - Unwrap the request if we have wrapped it.
	*/
	function unwrapRequest() { }


	/**
	* Abstract - Unwrap the response if we have wrapped it.
	*/
	function unwrapResponse() { }


	/**
	* Abstract - Create and return a request wrapper that has been inserted
	* in the appropriate spot in the request chain.
	*/
	function wrapRequest() { }


	/**
	* Abstract - Create and return a response wrapper that has been inserted
	* in the appropriate spot in the response chain.
	*/
	function wrapResponse() { }

}
?>