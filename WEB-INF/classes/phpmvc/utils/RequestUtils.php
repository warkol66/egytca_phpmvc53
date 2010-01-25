<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/classes/phpmvc/utils/RequestUtils.php,v 1.7 2006/05/17 07:10:52 who Exp $
* $Revision: 1.7 $
* $Date: 2006/05/17 07:10:52 $
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
* General purpose utility methods related to processing a servlet request
* in the Struts controller framework.
*
* @author John C. Wildenauer (php.MVC port)<br>
*  Credits:<br>
*  Craig R. McClanahan (original Struts class: see jakarta.apache.org)<br>
*  Ted Husted (original Struts class: see jakarta.apache.org)
* @version $Revision: 1.7 $
* @public
*/
class RequestUtils {

	// ----- Static Variables ----------------------------------------------- //

	/**
	* Commons Logging instance.
	* @private
	* @type Log
	*/
	var $log = NULL; // LogFactory.getLog(RequestUtils.class)

	/**
	* The default Locale for our server.
	* @private
	* @type Locale
	*/
	#private static final Locale defaultLocale = Locale.getDefault();

	/**
	* The message resources for this package.
	* @private
	* @type MessageResources
	*/
	#private static MessageResources messages =
	#MessageResources.getMessageResources("org.apache.struts.util.LocalStrings");

	/**
	* The context attribute under which we store our prefixes list.
	* @private
	* @type string
	*/
	var $PREFIXES_KEY = "PREFIXES";	// "org.apache.struts.util.PREFIXES"


	// ----- Constructors --------------------------------------------------- //

	function RequestUtils() {

		$this->log	= new PhpMVC_Log(); 
		$this->log->setLog('isDebugEnabled'	, False);
		$this->log->setLog('isInfoEnabled'	, False);
		$this->log->setLog('isTraceEnabled'	, False);

	}


	// ----- Public Methods ------------------------------------------------- //

	/**
	* Return a new instance of the specified class name,
	* after loading the class from this web application's class loader.
	* The specified class <strong>MUST</strong> have a public zero-arguments
	* constructor.
	*
	* @param string	Fully qualified class name to use
	* @public
	* @returns object
	*/
	function classLoader($className) {

		$debug = $this->log->getLog('isDebugEnabled');

		if($debug) {
			$this->log->debug("  RequestUtils->classLoader()[" . __LINE__ .
									"]: Loading class file '" . $className . ".php'" );
		}

		// Handle a fully qualified class name:
		// Like: "admin/LogonAction" or "admin\LogonAction" or "admin.LogonAction"

		// Replace any "\" or "." directory separator characters found in the 
		// fully qualified class name.
		if( strpos($className, '/') ) {
			// No transformation required
		} elseif( strpos($className, '\\') ) {
			// Transformation of '\' with '/'
			$className = str_replace('\\', '/', $className);
		} else {
			// Transformation of '.' with '/'
			$className = str_replace('.', '/', $className);
		}

		// Find the real class name (after a last "/", if any).
		// Like: "admin/[LogonAction]"
		$_className = '';			// The real class name. Like: LogonAction
		$_className = substr(strrchr($className, "/"), 1);
		if($_className == '') {
			// No "/" found, so assume that $className is the real class name
			$_className = $className;
		}

		// Try to open the required class file
		$classFileName = $className.'.php';
		// The requested class may already be loaded
		if(!class_exists($_className)) {
			include_once $classFileName; 
		}

		// Build the class file included class file - for now 
		$oClass = new $_className;

		return $oClass;

	}


	/**
	* Create (if necessary) and return an ActionForm instance appropriate
	* for this request.  If no ActionForm instance is required, return
	* <code>null</code>.
	*
	* @param HttpRequestBase		The server request we are processing
	* @param ActionConfig			The action mapping for this request
	* @param ApplicationConfig		The application configuration object
	*										for this sub-application
	* @param ActionServer			The ActionServer controller we are associated 
	*										with
	* @public
	* @returns ActionForm
	*/
	function createActionForm($request, $mapping, $appConfig, $actionServer) {

		$log_trace = $this->log->getLog('isTraceEnabled');

		// Is there a form bean associated with this mapping?
		// Form bean name or attribute scope name (see ActionConfig) !!
		$attribute = $mapping->getAttribute(); // String, 
		if($attribute == NULL) {
		#	return NULL;
		}

		// Look up the form bean configuration information to use
		// (name of the form bean associated with this action)
		$name = $mapping->getName(); // String (Eg: "logonForm")
		$config = $appConfig->findFormBeanConfig($name); // FormBeanConfig

		if($config == NULL) {
			return NULL;
		}

		// Look up any existing form bean instance
		$controllerCfg = $appConfig->getControllerConfig();
		$debug = $controllerCfg->getDebug();
		if($debug >= 2) {
			// DO LOGGING
			#LOG.info(" Looking for ActionForm bean instance in scope '" +
			#mapping.getScope() + "' under attribute key '" +
			#attribute + "'");
		}

		#### TO DO - SESSION MANAGEMENT ####
		$instance	= NULL; // ActionForm
		$session		= NULL; // HttpSession
		if( 'request' == $mapping->getScope() ) {

			#$instance = $request->getAttribute($attribute); // (ActionForm)

		} else {

			#$session	= $request->getSession();
			#$instance	= $session->getAttribute($attribute); // (ActionForm)
		}

		// Can we recycle the existing form bean instance (if there is one)?
		if($instance != NULL) {
			if($config->getDynamic()) {	// Dynamin Action Form
				$className = '';
				$actionForm = NULL;
				$actionForm = $instance->getDynaClass();
				$className = $actionForm->getName(); // String (DynaBean)
				if( $className == $config->getName() ) {

					$controllerConfig = $appConfig.getControllerConfig();

					if($controllerConfig->getDebug() >= 2) {
						// DO LOGGING
						#servlet.log(" Recycling existing DynaActionForm instance " +
						#of type '" + className + "'");
					}
					return $instance;
				}

			} else {

				# Java Object.getClass(), returns the runtime class of an object
				#String className = instance.getClass().getName(); // Name of the form bean
				$className = $instance->getName(); // ActionForm - Name of the form bean

				if( $className == $config->getType() ) {
					if($controllerConfig->getDebug() >= 2) {
						#servlet.log(" Recycling existing ActionForm instance " +
						#				"of class '" + className + "'");
					}
					return $instance;
				}
			}
		}

		// Create and return a new form bean instance
		if($config->getDynamic()) {

			$dynaClass = DynaActionFormClass::createDynaActionFormClass($config); // DynaActionFormClass
			$instance = $dynaClass->newInstance(); // (ActionForm)
			// Catch
				#LOG.error(servlet.getInternal().getMessage("formBean", config.getName()), t);
				#return (null);

		} else {

			# <<<<<<<<<<<<< >>>>>>>>>>>>>>>
			#$instance = $applicationInstance($config->getType()); // (ActionForm)
			$actionForm = $config->getType();
			$instance = $this->classLoader($actionForm); // (ActionForm)
			# <<<<<<<<<<<<< >>>>>>>>>>>>>>>

			// Catch
				#LOG.error(servlet.getInternal().getMessage("formBean", config.getType()), t);
				#return (null);

		}


		if($log_trace) {
			$this->log->trace("RequestUtils->createActionForm()[".__LINE__.
									"]: ActionForm:  '" . $actionForm."' extends " .
									"'". get_parent_class($actionForm) ."'" );
			
		}

		$instance->setActionServer($actionServer); // instance->setServlet(servlet)
		$instance->reset($mapping, $request);
		return $instance;

 	}


	/**
	* Populate the properties of the specified Bean from the specified
	* HTTP request, based on matching each parameter name (plus an optional
	* prefix and/or suffix) against the corresponding Beans "property
	* setter" methods in the bean's class.  Suitable conversion is done for
	* argument types as described under <code>setProperties()</code>.
	* <p>
	* If you specify a non-null <code>prefix</code> and a non-null
	* <code>suffix</code>, the parameter name must match <strong>both</strong>
	* conditions for its value(s) to be used in populating bean properties.
	* If the request's content type is "multipart/form-data" and the
	* method is "POST", the HttpRequest object will be wrapped in
	* a MultipartRequestWrapper object.
	*
	* @param object		The Bean whose properties are to be set
	* @param string		The prefix (if any) to be prepend to bean property
	*							names when looking for matching parameters
	* @param string		The suffix (if any) to be appended to bean property
	*							names when looking for matching parameters
	* @param HttpRequestBase	The HTTP request whose parameters are 
	*							to be used to populate bean properties
	*
	* @public
	* @returns void
	*/
	function populate(&$bean, $prefix, $suffix, $request) {

		// Build a list of relevant request parameters from this request
		$properties = array(); // HashMap
		// Iterator of parameter names
		$names = NULL; // Enumeration
		$multipartElements = NULL; // Hashtable for multipart values

		$contentType = $request->getContentType();	// String
		$method = strtoupper($request->getMethod()); // String
		$isMultipart = False;								// boolean

		// Tests if this string starts with "multipart/form-data".
		$multiPart = False;
		if( ereg("^multipart/form-data", $contentType) ) {
			$multiPart = True;
		}

		// Multipart form
		if( ($contentType != NULL) && ($multiPart == True) && ($method == 'POST') ) {

			// Get the ActionServletWrapper from the form bean
			// ...

			// Obtain a MultipartRequestHandler
			$multipartHandler = NULL; // LATER
			// ...
			
			// Set the multipart request handler for our ActionForm.
			// If the bean isn't an ActionForm, an exception would have been
			// thrown earlier, so it's safe to assume that our bean is
			// in fact an ActionForm.
			#((ActionForm) bean).setMultipartRequestHandler(multipartHandler);

			if($multipartHandler != NULL) {
				$isMultipart = True;
				// Set servlet and mapping info
				// ...
			}

			$request->removeAttribute(Action::getKey('MAPPING_KEY'));
		}

		// Standard form
		if(! $isMultipart) {
			$names = $request->getParameterNames();
		}

		foreach($names as $name) {

			// ...

		}


		// Retrieve the 'GET'/'POST' form data
		if($method == 'GET') {	
			$properties = $request->getGetVars();
		} elseif($method == 'POST') {
			$getProperties  = $request->getGetVars();
			$postProperties = $request->getPostVars();
			$properties = array_merge($getProperties, $postProperties);
		}

		// Set the corresponding properties of our bean
		$beanUtils = new PhpBeanUtils;
		$beanUtils->populate($bean, $properties);
		// Catch
		#	throw new ServletException("BeanUtils.populate", e);

	}


	/**
	* Try to locate a multipart request handler for this request. First, look
	* for a mapping-specific handler stored for us under an attribute. If one
	* is not present, use the global multipart handler, if there is one.
	*
	* <p>Returns the multipart handler to use, or <code>NULL</code> if none is
	* found.
	*
	* @param HttpRequestBase	The HTTP request for which the multipart handler
	*									should be found.        
	* @param ActionServer		The <code>ActionServletWrapper</code> processing
	*									the supplied request.
	* @returns
	*/
	function getMultipartHandler($request, $actionServer) { }


	/**
	* Return the URL representing the current request.  This is equivalent
	* to <code>HttpServletRequest.getRequestURL()</code> in Servlet 2.3.
	*
	* @param HttpRequestBase The server request we are processing
	* @public
	* @returns
	*/
	function requestURL($request) { }


	/**
	* Return the URL representing the scheme, server, and port number of
	* the current request.  Server-relative URLs can be created by simply
	* appending the server-relative path (starting with '/') to this.
	*
	* @param HttpRequestBase	The server request we are processing
	* @public
	* @returns
	*/
	function serverURL($request) { }


	/**
	* Select the sub-application to which the specified request belongs, and
	* add corresponding request attributes to this request.
	*
	* @param HttpRequestBase	The servlet request we are processing
	*									[as a reference (&$request) !!!]
	* @param AppServletContext	The AppServletContext for this web application
	* @public
	* @returns void
	*/
	function selectApplication(&$request, $context) {

		// Acquire the path (String) used to compute the sub-application
		$matchPath = $request->getAppServerPath();	// getServletPath()

		// Match against the list of sub-application prefixes
		$prefix = '';
		$prefixes = $this->getApplicationPrefixes($context);	// String[] !!

		if($prefixes != NULL) {
			foreach($prefixes as $prefixVal) {
				//  Tests if this string starts with the specified prefix.
				if( ereg("^$prefixVal", $matchPath) ) {// matchPath.startsWith(prefixes[i])
					$prefix = $prefixVal;
					break;
				}
			}
		}

		// Expose the resources for this sub-application
		// ApplicationConfig
		$config = $context->getAttribute(Action::getKey('APPLICATION_KEY').$prefix);

		if($config != NULL)
			$request->setAttribute(Action::getKey('APPLICATION_KEY'), $config);

		// MessageResources
		$resources = $context->getAttribute(Action::getKey('MESSAGES_KEY').$prefix);

		if($resources != NULL)
			$request->setAttribute(Action::getKey('MESSAGES_KEY'), $resources);

	}


	/**
	* Return the list of sub-application prefixes that are defined for
	* this web application, creating it if necessary.  <strong>NOTE</strong> -
	* the "" prefix for the default application is not included in this list.
	*
	* @param AppServletContext The AppServletContext for this web application
	* @public
	* @returns
	*/
	function getApplicationPrefixes($context) {
		// stub
		return NULL;
	}

}
?>