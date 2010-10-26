<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/classes/phpmvc/action/RequestProcessor.php,v 1.15 2006/05/21 22:08:29 who Exp $
* $Revision: 1.15 $
* $Date: 2006/05/21 22:08:29 $
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
* <p><strong>RequestProcessor</strong> contains the processing logic that
* the php.MVC controller performs as it receives each server request.
* You can customize the request processing behavior by subclassing this 
* class and overriding the method(s) whose behavior you are
* interested in changing.</p>
*
* @author John C. Wildenauer (php.MVC port)<br>
*  Credits:<br>
*  Craig R. McClanahan (Tomcat/catalina class: see jakarta.apache.org)
* @version $Revision: 1.15 $
* @public
*/
class RequestProcessor {

	// ----- Manifest Constants --------------------------------------------- //

	/**
	* The request attribute under which the path information is stored for
	* processing during a RequestDispatcher.include() call.
	* @type string
	*/
	var $INCLUDE_PATH_INFO = 'phpmvc.appserver.include.path_info';

	/**
	* The request attribute under which the servlet path information is stored
	* for processing during a RequestDispatcher.include() call.
	* @type string
	*/
	var $INCLUDE_APPSERVER_PATH = 'phpmvc.appserver.include.appserver_path';


	// ----- Protected Instance Variables ----------------------------------- //

	/**
	* The set of Action instances that have been created and initialized,
	* keyed by the fully qualified Java class name of the Action class.
	* @private
	* @type array
	*/
	var $actions = array();	// new HashMap()


	/**
	* The ApplicationConfiguration we are associated with.
	* @private
	* @type ApplicationConfig
	*/
	var $appConfig = NULL;


	/**
	* Commons Logging instance.
	* @private
	* @type Log
	*/
	var $log = NULL; // LogFactory.getLog(this.getClass())


	/**
	* The ActionServer controller we are associated with.
	* @private
	* @type ActionServer
	*/
	var $actionServer = NULL;


	// ----- Constructors --------------------------------------------------- //

	function RequestProcessor () {

		$this->log	= new PhpMVC_Log();
		$this->log->setLog('isDebugEnabled'	, False);
		$this->log->setLog('isInfoEnabled'	, False);
		$this->log->setLog('isTraceEnabled'	, False);

	}


	// ----- Public Methods ------------------------------------------------- //

	/**
	* Clean up in preparation for a shutdown of this application.
	*
	* @public
	* @returns void
	*/
	function destroy() {

		$actions = array_keys($this->actions);
		foreach($actions as $action) {
			$action->setActionServer(NULL);
		}
		$this->actions = array();
		$this->actionServer = NULL;

	}


	/**
	* Initialize this request processor instance.
	*
	* @param ActionServer The ActionServer we are associated with
	* @param ApplicationConfig The ApplicationConfig we are associated with.
	*
	* @public
	* @returns void
	*/
	function init(&$actionServer, &$appConfig) {

		$this->actions = array();
		$this->actionServer =& $actionServer;
		$this->appConfig =& $appConfig;

	}


	/**
	* <p>Process an <code>HttpServletRequest</code> and create the
	* corresponding <code>HttpServletResponse</code>.</p>
	*
	* @param HttpRequestBase	The server request we are processing
	* @param HttpResponseBase	The server response we are creating
	* @public
	* @returns void
	*/
	function process($request, $response) {

		$debug = $this->log->getLog('isDebugEnabled');
		$trace = $this->log->getLog('isTraceEnabled');

		if($trace) {
			$this->log->trace('Start: RequestProcessor->process(...)'.
									'['.__LINE__.']');
		}


		// Process Actions until shutdown == True
		$shutdown = False;
		while(!$shutdown) {

			// Wrap multipart requests with a special wrapper
			#request = processMultipart($request);

			// Identify the path component we will use to select a mapping
			// Note: We catch a no-path request in processMapping()
			//        - if a default action set: (unknown=True) - use that action mapping
			//        - else we bail out if no valid [path => action-mapping] exists
			$path = $this->processPath($request, $response); // String (eg: '/login'
			#if($path == NULL) {
			#	return;	
			#}

			if($debug) {
				$this->log->debug("RequestProcessor->process()[".__LINE__.
										"]: Processing a '".$request->getMethod().
	                     		"' request for path '" .$path."'");
			}

			// Select a Locale for the current user if requested
			#$this->processLocale($request, $response);

			// Set the content type and no-caching headers if requested
			$this->processContent($request, $response); //default type 'text/html'
			$this->processNoCache($request, $response); //no-cache headers, if req'd

			// General purpose preprocessing hook - override in ActionServer subclass
			if(!$this->processPreprocess($request, $response, $path)) {
				return;
			}

			// Identify the mapping for this request (ActionConfig)
			$mapping = $this->processMapping($request, $response, $path);
			if($mapping == NULL) {
				return;
			}

			// Check for any authentication role required to perform this action
			if (!$this->processRoles($request, $response, $mapping)) {
				return;
			}

			// Retrieve and return the ActionForm bean associated 
			//  with this mapping
			$form = $this->processActionForm($request, $response, $mapping);	// ActionForm

			$this->processPopulate($request, $response, $form, $mapping);
			if(!$this->processValidate($request, $response, $form, $mapping)) {
				return;
			}

			// Process a forward specified by this mapping (normally returns True)
			// Note: mapping->getForward() returns NULL for standard processing
			if(!$this->processForward($request, $response, $mapping)) {
				// An alternate "forward" action was set previously, and the 
				// $this->doForward(...) called. So we're done.
				return;
			}

			// Process an include specified by this mapping (normally returns True)
			// Note: mapping->getInclude() returns NULL for standard processing
			if(!$this->processInclude($request, $response, $mapping)) {
				// An alternate "include" action was set previously, and the 
				// $this->doInclude(...) called. So we're done.
				return;
			}

			// Create or acquire the Action instance to process this request
			$action = $this->processActionCreate($request, $response, $mapping); // Action
			if($action == NULL) {
				return;
			}

			// Call the Action instance itself
			$forward = $this->processActionPerform($request, $response, 
																$action, $form, $mapping);	// ActionForward

			// Check if we have another Action to process
			$shutdown = $this->processActionChain($request, $forward);

			// Process the returned ActionForward instance.
			// No Forward processing occures if $forward->getPath() == NULL
			$this->processActionForward($request, $response, $forward);

		} // while(...)


	}


	// ----- Processing Methods --------------------------------------------- //

	/**
	* Return an <code>Action</code> instance that will be used to process
	* the current request, creating a new one if necessary.
	*
	* @param HttpRequestBase	The http request we are processing
	* @param HttpResponseBase	The http response we are creating
	* @param ActionConfig		The mapping we are using
	* @public
	* @returns Action
	*/
	function processActionCreate($request, $response, $mapping) {

		$debug = $this->log->getLog('isDebugEnabled');
		$trace = $this->log->getLog('isTraceEnabled');

		if($trace) {
			$this->log->trace('Start: RequestProcessor->processActionCreate(...)'.
									'['.__LINE__.']');
		}

		// Acquire the Action instance we will be using (if there is one)
		$className = $mapping->getType(); // String

		if($debug) {
			$this->log->debug(" Looking for Action instance for class '".$className."'");
		}

		$instance = NULL;	// Action

		// Return any existing Action instance of this class
		if(array_key_exists($className, $this->actions))
			$instance = $this->actions[$className];	// (Action)

		if($instance != NULL) {
			if($trace) {
				$this->log->trace("  Returning existing Action instance");
 			}

			return $instance;
		}

		// Create and return a new Action instance
		if($trace) {
			$this->log->trace("  Creating new Action instance '".$className."'");
		}

		$requestUtils = new RequestUtils;
		$instance = $requestUtils->classLoader($className); // Action
		$instance->setActionServer($this->actionServer);

		$this->actions['className'] =&  $instance;
		
		// Catch
		// ........

		return $instance;

	}


	/**
	* Retrieve and return the <code>ActionForm</code> bean associated with
	* this mapping, creating and stashing one if necessary.  If there is no
	* form bean associated with this mapping, return <code>NULL</code>.
	*
	* @param HttpRequestBase	The server request we are processing
	* @param HttpResponseBase	The server response we are creating
	* @param ActionConfig		The mapping we are using
	* @access private
	* @returns ActionForm
	*/
	function processActionForm($request, $response, $mapping) {

		// Create (if necessary) a form bean to use
		$requestUtils = new RequestUtils;
		$instance = $requestUtils->createActionForm($request, $mapping, 
																$this->appConfig, 
																$this->actionServer); // ActionForm

		if($instance == NULL) {
			return NULL;
		}

		// Store the new instance in the appropriate scope
		$debug = $this->log->getLog('isDebugEnabled');
		if($debug) {
			$this->log->debug(" Storing ActionForm bean instance in scope '" .
			$mapping->getScope() . "' under attribute key '" .
			$mapping->getAttribute() . "'");
		}

		if('request' == $mapping->getScope()) {
			$request->setAttribute($mapping->getAttribute(), $instance);
		} else {
			# TO-DO setup session handling
			#$session = $request->getSession();	// HttpSession
			#$session->setAttribute($mapping->getAttribute(), $instance);
		}

		return $instance;

	}


	/**
	* Forward or redirect to the specified destination, by the specified
	* mechanism.
	*
	* <p>A "forward" request is handled within the current processor. The RequestProcessor
	* simply passes control to the ActionDispatcher, which "includes" the specified
	* URI (page/template). This is the default behavour:
	* <pre>
	*   &lt;forward 
	*     name="forward_path 
	*     path="/phpmvc-test/TestMVC/Main.php?do=forwardRequest" 
	*     redirect="false"/&gt;
	* </pre>
	* </p>
	* <p>A "redirect" request actually sends the client (browser) a header response
	* redirecting the client to a <b>new</b> URL. Execution of the current process
	* terminates immediately on sending the request redirect header to the client.
	*
	* <pre>
	*   &lt;!-- This server --&gt;
	*   &lt;forward 
	*     name="redirect_path 
	*     path="/phpmvc-test/MyApp/Main.php?do=newRequest" 
	*     redirect="true"/&gt;
	* </pre>
	* </p>
	* <p>or
	* <pre>
	*   &lt;!-- This server, or a remote server --&gt;
	*   &lt;forward 
	*     name="redirect_path 
	*     path="http://www.myhost.com/MyApp/Main.php?do=newRequest" 
	*     redirect="true"/&gt;
	* </pre>
	* </p>
	*	
	* @param HttpRequestBase	The server request we are processing
	* @param HttpResponseBase	The server response we are creating
	* @param ForwardConfig		The ForwardConfig object controlling where we go next
	* @private
	* @returns void
	*/
	function processActionForward(&$request, &$response, $forward) {

		$debug = $this->log->getLog('isDebugEnabled');
		$trace = $this->log->getLog('isTraceEnabled');

		if($trace) {
			$this->log->trace('Start: RequestProcessor->processActionForward(...)'.
									'['.__LINE__.']');
		}

		if($forward == NULL) {
			return;
		}

		$path = $forward->getPath();	// String	
		if($path == NULL) {
			return;
		}


		// Check if we are doing a Redirect (client-side)
		if( strtolower($forward->getRedirect()) == True) {

			// www.myhost.com
			$host = $_SERVER['HTTP_HOST'];

			// Encode URLs using htmlentities((urlencode($data))
			// Users should encode their request query strings in their methods
			//$path = htmlentities(urlencode($path));


			// Tests if this string starts with "/".
			if( substr($path, 0, 1) == '/' ) {

				// ContextPath: "http://www.myhost.com/{context/path}/Main.php?do=myAction"
				// contextRelative:  Set this to "true" if, in a modular application, 
				// the path attribute starts with a slash "/" and should be considered
				// relative to the entire web application rather than the module.
				// Default is False.
				// Ref: phpmvc-config_xx.dtd
				if($forward->getContextRelative()) {
					$path = $request->getContextPath().$path;
				} else {
					// default
					$path = $request->getContextPath() .
                        $this->appConfig->getPrefix().$path; // ApplicationConfig 
				}
			}

			// Build a fully qualified path scheme
			// Scheme:
			// The name of the scheme used to make this request, for example, 
			// http, https, or ftp. Different schemes have different rules for 
			// constructing URLs, as noted in RFC 1738. 
			// Ref: Java Interface ServletRequest.getScheme()
			$pathScheme = '';
			$scheme = '';	// Note: $_SERVER['HTTPS'] appears to be undocumented !!!
			if(is_array($_SERVER)) {
				// $_SERVER were introduced in PHP v 4.1.0
				$scheme = (@$_SERVER['HTTPS'] == 'on' ? 'https' : 'http');
			} elseif(is_array($HTTP_SERVER_VARS)) {
				// Before PHP v 4.1.0 (No one should be using this version now)
				$scheme = (@$HTTP_SERVER_VARS['HTTPS'] == 'on' ? 'https' : 'http');
			} else {
				// Catch all
				$scheme = 'http';
			}

			// $path = "{http://}www.myhost.com/..."
			if( !preg_match("/^([a-z]+):\/\//i", $path) ) {

				if($scheme == 'http' || $scheme == 'https' || $scheme == 'ftp') {
					$pathScheme = $scheme.'://';
				}

				// We may need to silently add the leading slash to the path.
				if( substr($path, 0, 1) != '/' ) {
					$path = "/".$path;
				}

				$redirPath = $pathScheme.$host.$path; // $path starts with "/"

			} else {

				// $path = "http://www.myhost.com/..."
				$redirPath = $path;

			}

			// Send the redirect request to the client, and we are done.
			header("Location: $redirPath"); 
			exit; // kill this processor now

		// Else we do a Forward (server-side). Default action
		} else {

			// Tests if this string starts with "/".
			$leadingPathSlash = False;
			// Tests if this string starts with "/".
			if( substr($path, 0, 1) == '/' ) {
				$leadingPathSlash = True;
			}

			if($leadingPathSlash && !$forward->getContextRelative()) {
				$path = $this->appConfig->getPrefix() . $path;
			}

			$this->doForward($path, $request, $response);
		}

	}


	/**
	* Ask the specified <code>Action</code> instance to handle this
	* request.  Return the <code>ActionForward</code> instance (if any)
	* returned by the called <code>Action</code> for further processing,
	* or NULL if using the ForwardAction class.
	*
	* @param HttpRequestBase	The server request we are processing
	* @param HttpResponseBase	The server response we are creating
	* @param Action				The Action instance to be used
	* @param ActionForm			The ActionForm instance to pass to this Action
	* @param ActionMapping		The ActionMapping instance to pass to this Action
	* @private
	* @returns ActionForward
	*/
	function processActionPerform(&$request, &$response, $action, $form, $mapping) {

		$debug = $this->log->getLog('isDebugEnabled');
		$trace = $this->log->getLog('isTraceEnabled');

		if($trace) {
			$this->log->trace('Start: RequestProcessor->processActionPerform(...)'.
									'['.__LINE__.']');
		}


		## TO-DO ERROR HANDLING
		$actionForward = NULL;
		$actionForward = $action->execute($mapping, $form, $request, $response);

		// Catch
		if($actionForward == NULL) {
			#return 'processException ...';
			return NULL;
		}

		return $actionForward;

	}


	/**
	* Examine this <code>ForwardConfig</code> instance to determine if a path
	* to another Action resource (an Action chain) has been set.<br>
	*
	* <p>Example xml action-mapping element:
	*
	*   <pre>
	*   &lt;action path="confirmOrder"
	*
	*      type="ConfirmOrderAction"/&gt;
	*
	*      &lt;forward 
	*         name="confirm_order_success" 
	*         path="confitmPage.php" 
	*         nextActionPath="confirmEmailAction"/&gt;
	*
	*   &lt;/action&gt;
	*   </pre>
	*
	* <p>Returns <code>True</code> if a "nextActionPath" has been set to the
	* next Action in an Action chain.<br>
	* Returns <code>False</code> if no "nextActionPath" has been set.
	* Processing will terminate when no more Action paths are in the Action
	* chain, and any current static resources (pages) have been processed.
	*
	* @param HttpRequestBase	The server request we are processing
	* @param ForwardConfig		The ForwardConfig controlling where we go next
	* @private
	* @returns Boolean
	*/
	function processActionChain(&$request, $forward) {

		$debug = $this->log->getLog('isDebugEnabled');
		$trace = $this->log->getLog('isTraceEnabled');

		if($trace) {
			$this->log->trace('Start: RequestProcessor->processActionChain(...)'.
									'['.__LINE__.']');
		}	

		if($forward == NULL) {
			return True;	// no forwarding instructions, so we shutdown
		}

		$nextActionPath = '';	// string
		$nextActionPath = $forward->getNextActionPath();

		if($nextActionPath == '') {
			// No more Actions to process, so prepare to shutdown
			return True; // shutdown == True
		} else {
			// Set the path to the next Action in the request.
			// Eg: "confirmEmailAction"
			$request->setAttribute('ACTION_DO_PATH', $nextActionPath); 
			return False; // shutdown == false
		}

	}


	/**
	* Set the default content type (with optional character encoding) for
	* all responses if requested.  <strong>NOTE</strong> - This header will
	* be overridden automatically if a
	* <code>RequestDispatcher.forward()</code> call is ultimately invoked.
	*
	* @param HttpRequestBase	The server request we are processing
	* @param HttpResponseBase	The server response we are creating
	* @private
	* @returns void
	*/
	function processContent($request, $response) {

		// ApplicationConfig->getControllerConfig()
		$controllerConfig = $this->appConfig->getControllerConfig();
		$contentType = $controllerConfig->getContentType(); // default 'text/html'

		if($contentType != NULL) {
			$response->setContentType($contentType);
		}

	}


	/**
	* Process a forward requested by this mapping (if any).  Return
	* <code>true</code> if standard processing should continue, or
	* <code>false</code> if we have already handled this request.
	*
	* @param HttpRequestBase	The server request we are processing
	* @param HttpResponseBase	The server response we are creating
	* @param ActionMapping		The ActionMapping we are using
	* @private
	* @return boolean
	*/
	function processForward($request, $response, $mapping) {

		$debug = $this->log->getLog('isDebugEnabled');
		$trace = $this->log->getLog('isTraceEnabled');

		if($trace) {
			$this->log->trace('Start: RequestProcessor->processForward(...)'.
									'['.__LINE__.']');
		}


		// Are we going to processing this request?
		$forward = $mapping->getForward();	// String
		if($forward == NULL) {
			// No forward mapping (override) has been set elsewhere
			// so we continue normal processing.
			return True;
		}

		/////
		// A forward mapping uri has been set to override normal processing

		// Unwrap the multipart request (if any)
		//  the object is of 'class' or has 'class' as one of its parents
		if(is_a($request, 'MultipartRequestWrapper')) {	// instanceof
			$request = $request->getRequest();
		}

		// Construct a request dispatcher for the specified path
		$uri = $this->appConfig->getPrefix().$forward;	// String

		// Delegate the processing of this request
		// FIXME - exception handling?

		if($debug) {
			$this->log->debug(" Delegating via forward to '" . $uri . "'");
		}

		$this->doForward($uri, $request, $response);

		return False;

    }


	/**
	* Process an include requested by this mapping (if any).  Return
	* <code>true</code> if standard processing should continue, or
	* <code>false</code> if we have already handled this request.
	*
	* @param HttpRequestBase	The server request we are processing
	* @param HttpResponseBase	The server response we are creating
	* @param ActionMapping		The ActionMapping we are using
	* @private
	* @returns boolean
	*/
	function processInclude($request, $response, $mapping) {

		$debug = $this->log->getLog('isDebugEnabled');

		// Are we going to processing this request?
		$include = $mapping->getInclude();	// String
		if($include == NULL) {
			// No include mapping (override) has been set elsewhere
			// so we continue normal processing.
			return True;
		}

		/////
		// An include mapping uri has been set to override normal processing

		// Unwrap the multipart request (if any)
		//  the object is of 'class' or has 'class' as one of its parents
		if(is_a($request, 'MultipartRequestWrapper')) {	// instanceof
			$request = $request->getRequest(); // cast (MultipartRequestWrapper)
		}

		// Construct a request dispatcher for the specified path
		$uri = $this->appConfig->getPrefix() . $include; // String
		
		// Delegate the processing of this request
		// FIXME - exception handling?

		if($debug) {
			$this->log->debug(" Delegating via include to '" . $uri + "'");
		}

		$this->doInclude($uri, $request, $response);

		return False;

	}


	/**
	* Select the mapping used to process the selection path for this request.
	* If no mapping can be identified, create an error response and return
	* <code>NULL</code>.
	*
	* @param HttpRequestBase	The server request we are processing
	* @param HttpResponseBase	The server response we are creating
	* @param string	The portion of the request URI for selecting a mapping
	* @private
	* @returns ActionMapping
	*/
	function processMapping($request, $response, $path) {

		$debug = $this->log->getLog('isDebugEnabled');
		$trace = $this->log->getLog('isTraceEnabled');

		if($trace) {
			$this->log->trace('Start: RequestProcessor->processMapping(...)'.
									'['.__LINE__.']');
		}

		// Is there a directly defined mapping for this path?
		//		ActionConfig for this Request path 
		//		Eg: ApplicationConfig->actionConfigs[$path]
		$mapping = $this->appConfig->findActionConfig($path); // ActionConfig

		if($mapping != NULL) {
			// Eg: "phpmvc.action.mapping.instance" => ActionConfig
			$request->setAttribute(Action::getKey('MAPPING_KEY'), $mapping);
			return $mapping;
		}
		
		//obtengo el modulo a partir del path
		$match = preg_split("/[A-Z]/", $path);
		if (count($match) > 1) {
			$module = $match[0];

			//chequeo si existe el action correspondiente
			global $moduleRootDir;
			$expectedFile = $moduleRootDir."WEB-INF/classes/modules/".$module."/actions/".ucwords($path)."Action.php";
			//echo $expectedFile;
			if (file_exists($expectedFile)) {

				$newActionConfig = new ActionConfig();
				$newActionConfig->setName($path);
				$newActionConfig->setPath($path);
				$newActionConfig->setType(ucwords($path)."Action");
				
				//los forwards dependen del tipo de accion
				//moduloList -> [success -> ModuloList.tpl]
				//ModuloEdit -> [success -> ModuloEdit.tpl]
				//ModuleDoEdit -> [success -> /Main.php?do=ModuloList&amp;message=ok, redirect="true", failure -> ModuleEdit.tpl]
				//ModuleDoDelete -> [success -> /Main.php?do=ModuloList&amp;message=ok, redirect="true"]
				//Por Defecto -> [success -> Path.tpl]
				
				$forwardsRules = array();
				$forwardsRules["DoEdit"] = array();
				$forwardsRules["DoEdit"]["success"] = "/Main.php?do=MODULEList&message=ok";
				$forwardsRules["DoEdit"]["failure"] = "MODULEEdit.tpl";
				$forwardsRules["DoDelete"] = array();
				$forwardsRules["DoDelete"]["success"] = "/Main.php?do=MODULEList&message=ok";		
				
				$action = str_replace($module,"",$path);
				
				$forwards = false;
				$moduleSection = "";

				//function para tener before_filter en php < 5.3
				function strstrb($h,$n){
				    return array_shift(explode($n,$h,2));
				}
				
				foreach ($forwardsRules as $key => $rules) {
					if (preg_match("/".$key."$/",$action)) {
						$forwards = $rules;
						$moduleSection = strstrb($action, $key);
					}
				} 
				
				//si el forward tiene alguna regla especial
				if (!empty($forwards)) {
					foreach ($forwards as $forwardName => $forwardPath) {
						$forwardObject = new ForwardConfig();
						$forwardObject->setName($forwardName);
						
						//si no termina en tpl le pongo redirect true
						if (substr($forwardPath, strlen($forwardPath)-3) != "tpl") {
							$forwardObject->setRedirect(true);
							$moduleForPath = $module . $moduleSection;
						} else {
							$moduleForPath = ucwords($module) . $moduleSection;
						}
							
						//obtengo el path reemplazando MODULE por el modulo real	
						$forwardObject->setPath(str_replace("MODULE", $moduleForPath, $forwardPath));
											
						$newActionConfig->addForwardConfig($forwardObject);						
					}
				} else {
				//sino es un forward que usamos las reglas por defecto
					$successForward = new ForwardConfig();
					$successForward->setName("success");
					$successForward->setPath(ucwords($path).".tpl");
					$newActionConfig->addForwardConfig($successForward);					
				}
				
				$this->appConfig->addActionConfig($newActionConfig);
				$mapping = $this->appConfig->findActionConfig($path);
				return $mapping;
			}			
		}
		
		// Locate the mapping for unknown paths (if any)
		$configs = NULL;
		$configs = $this->appConfig->findActionConfigs(); // ActionConfig
		if($configs != NULL) {
			foreach($configs as $config) {
				// Check if "unknown" is a boolean ***and*** is True
				if($config->getUnknown() === True) {
					$mapping = $config; // cast (ActionMapping)
					$request->setAttribute(Action::getKey('MAPPING_KEY'), $mapping);
					return $mapping;
				}
			}
		}

		// No mapping can be found to process this request
		#$this->log->error($this->getInternal()->getMessage("processInvalid", $path));
		#response.sendError(HttpServletResponse.SC_BAD_REQUEST,
      #                     getInternal().getMessage
      #                     ("processInvalid", path));
		
		#See: ActionResources.properties: 
		#		processInvalid=Invalid path {0} was requested

		$this->log->error("RequestProcessor->processMapping()[".__LINE__."]: Invalid path '$path' was requested");
		return NULL;

	}


	/**
	* If this is a multipart request, wrap it with a special wrapper.
	* Otherwise, return the request unchanged.
	*
	* @param HttpRequestBase The HttpServer request we are processing
	* @private
	* @return HttpRequestBase
	*/
	function processMultipart($request) {

		if('POST' != $request->getMethod()) {
			return $request;
		}

		$contentType = $request->getContentType(); // String

		$multiPart = False;
		if( ereg("^multipart/form-data", $contentType) )
			$multiPart = True;

		if( ($contentType != NULL) && ($multiPart == True) ) {
			$multiPartWrapper = new MultipartRequestWrapper($request);
			return $multiPartWrapper;
		} else {
			return $request;
		}

	}


	/**
	* Set the no-cache headers for all responses, if requested.
	* <strong>NOTE</strong> - This header will be overridden
	* automatically if a <code>RequestDispatcher.forward()</code> call is
	* ultimately invoked.
	*
	* @param HttpRequestBase The servlet request we are processing
	* @param HttpRequestBase The servlet response we are creating
	* @private
	* @returns void
	*/
	function processNoCache($request, $response) {

		$controllerConfig = $this->appConfig->getControllerConfig();
		if($controllerConfig->getNocache()) {
			$response->setHeader("Pragma", "No-cache");
			$response->setHeader("Cache-Control", "no-cache");
			$response->setDateHeader("Expires", 1);
		}

	}


	/**
	* Identify and return the path component (from the request URI) that
	* we will use to select an ActionMapping to dispatch with. If no such
	* path can be identified, create an error response and return
	* <code>NULL</code>.
	*
	* @param HttpRequestBase	The server request we are processing
	* @param HttpResponseBase	The server response we are creating
	* @private
	* @returns string
	*/
	function processPath(&$request, &$response) {

		$debug = $this->log->getLog('isDebugEnabled');
		$trace = $this->log->getLog('isTraceEnabled');

		if($trace) {
			$this->log->trace('Start: RequestProcessor->processPath(...)'.
									'['.__LINE__.']');
		}

		$path = NULL;	// String

		// For prefix matching, match on the path info (if any)
		$path = $request->getAttribute('INCLUDE_PATH_INFO'); // (String) !!!!		
		if($path == NULL) {
			// Get the path information associated with this Request
			$path = $request->getPathInfo();
		}

		if( ($path != NULL) && (strlen($path) > 0) ) {
			return $path;
		}

		// For extension matching, strip the application prefix and extension
		$path = $request->getAttribute('INCLUDE_APPSERVER_PATH'); // (String)
		if($path == NULL) {
			$path = $request->getAppServerPath();
		}

		$prefix = $this->appConfig->getPrefix();	// String

		// ! path.startsWith(prefix)
		if(!preg_match("/^$prefix/", $path)) {

			#log.error(getInternal().getMessage("processPath",
			#	request.getRequestURI()));
			#response.sendError(HttpServletResponse.SC_BAD_REQUEST,
         #                      getInternal().getMessage
         #                      ("processPath", request.getRequestURI()));

			return NULL;
		}

		$path = substr($path, strlen($prefix));

		$slash = $period = 0; // Integer
		#int slash = path.lastIndexOf("/");
		$slash = strrpos($path, '/'); // last occurrence of char in string 

		#int period = path.lastIndexOf(".");
		$period = strrpos($path, '.');

		if(($period >= 0) && ($period > $slash)) {
			$path = substr($path, 0, $period);
		}


		// <<<<<<<<<<<<<<<< >>>>>>>>>>>>>>>>>>>>
		$path = $request->getAttribute('ACTION_DO_PATH');
		if($path != '') {
			if($trace)
				$this->log->debug('  RequestProcessor->processPath(...)'.
										'Found an Action path "'. $path. '" for this request'.
										'['.__LINE__.']');
		} else {
			return NULL;
		}
		// <<<<<<<<<<<<<<<< >>>>>>>>>>>>>>>>>>>>

		return $path;

	}


	/**
	* Populate the properties of the specified ActionForm instance from
	* the request parameters included with this request.
	*
	* @param HttpRequestBase	The server request we are processing
	* @param HttpResponseBase	The server response we are creating
	* @param ActionForm			The ActionForm instance we are populating
	* @param ActionMapping		The ActionMapping we are using
	* @private
	* @returns void
	*/
	function processPopulate($request, $response, &$form, $mapping) {

		$debug = $this->log->getLog('isDebugEnabled');
		$trace = $this->log->getLog('isTraceEnabled');

		if($trace) {
			$this->log->trace('Start: RequestProcessor->processPopulate(...)'.
									'['.__LINE__.']');
		}


		if($form == NULL) {
			return;
		}

		// Populate the bean properties of this ActionForm instance
		if($debug) {
			$this->log->debug(" Populating bean properties from this request");
		}

		$form->reset($mapping, $request);
		if($mapping->getMultipartClass() != NULL) {
			$request->setAttribute(Action::getKey('MULTIPART_KEY'),
											$mapping->getMultipartClass());
		}

		RequestUtils::populate($form, $mapping->getPrefix(), 
										$mapping->getSuffix(), $request);
		$form->setActionServer($this->actionServer); // form.setServlet(this.servlet)

	}


	/**
	* General-purpose preprocessing hook that can be overridden as required
	* by subclasses.  Return <code>true</code> if you want standard processing
	* to continue, or <code>false</code> if the response has already been
	* completed.  The default implementation does nothing.
	*
	* @param HttpRequestBase	The server request we are processing
	* @param HttpResponseBase	The server response we are creating
	* @param string	The portion of the request URI for selecting a mapping
	* @private
	* @returns boolean
	*/
	function processPreprocess(&$request, &$response, &$path) {

        return True;

    }


	/**
	* If this action is protected by security roles, make sure that the
	* current user possesses at least one of them.  Return <code>True</code>
	* to continue normal processing, or <code>False</code> if an appropriate
	* response has been created and processing should terminate.
	*
	* @param HttpRequestBase	The server request we are processing
	* @param HttpResponseBase	The server response we are creating
	* @param ActionMapping		The mapping we are using
	* @private
	* @returns boolean
	*/
	function processRoles($request, $response, $mapping) {

		$debug = $this->log->getLog('isDebugEnabled');

		// Is this action protected by role requirements?
		$roles = NULL;								// Array of strings
		$roles = $mapping->getRoleNames();	// <action ... roles= "admin0,admin1" ... />
		if(($roles == NULL) || (count($roles) < 1)) {
			// No roles security constraint for this action path, so user is good to go
			return True;
		}

		// If no user Principal exists for this user yet, we try to create one.
		$appServerContext =& $this->actionServer->appServerConfig->getAppServerContext();
		if($request->getUserPrincipal() == Null) {
			$oPrincipal = AuthenticatorBase::invoke($request, $response, $appServerContext);
			$request->setUserPrincipal($oPrincipal);
			if($oPrincipal == Null) {
				// If we cannot create a Principal, user cannot access this action path
				if($debug) {
					$this->log->debug(" Failed to create a Principal for user '" . 
												$request->getRemoteUser() . "'");
				}
				return False;
			}	
		}

		// Check the current user against the list of required roles
		$oRealm = $appServerContext->getRealm();		
		foreach($roles as $role) {
			if($request->isUserInRole($role, $oRealm)) {
				// This user has an access role (say: 'admin1') matching the defined action 
				// role, so she is good to go. 
				// Like: <action ... roles= "admin0,admin1" ... />
				if($debug) {
					$this->log->debug(" User '" . $request->getRemoteUser() .
 					"' has role '" . $role . "', granting access");
				}

				return True;
 
			}
		}

		// The current user is not authorized for this action
		if($debug) {
			$this->log->debug(" User '" . $request->getRemoteUser() .
                      "' does not have any required role, denying access");
		}

		#$response.sendError(HttpServletResponse.SC_BAD_REQUEST,
      #                     getInternal().getMessage("notAuthorized",
      #                                              mapping.getPath()));

		return False;

	}


	/**
	* Call the <code>validate()</code> method of the specified ActionForm,
	* and forward back to the input form if there are any errors.  Return
	* <code>true</code> if we should continue processing, or return
	* <code>false</code> if we have already forwarded control back to the
	* input form.
	*
	* @param HttpRequestBase	The servlet request we are processing
	* @param HttpResponseBase	The servlet response we are creating
	* @param ActionForm			The ActionForm instance we are populating
	* @param ActionMapping		The ActionMapping we are using
	* @private
	* @returns boolean
	*/
	function processValidate(&$request, &$response, &$form, &$mapping) {

		$debug = $this->log->getLog('isDebugEnabled');
		$trace = $this->log->getLog('isTraceEnabled');

		if($trace) {
			$this->log->trace('Start: RequestProcessor->processValidate(...)'.
									'['.__LINE__.']');
		}

		if($form == NULL) {
			return True;
		}

		// Was this submit cancelled?
		//   request->getParameter(Constants.CANCEL_PROPERTY) !!!!!!!!!!!!
		// FIX_THIS
		if(($request->getParameter('CANCEL_PROPERTY') != NULL) ||
			($request->getParameter('CANCEL_PROPERTY_X') != NULL)) {
			if($debug) {
				$this->log->debug(" Cancelled transaction, skipping validation");
			}

			return True;

		}

		// Has validation been turned off for this mapping?
		if($mapping->getValidate() == False) {
			return True;	// Consider form validation as done
		}

		// Call the form bean's validation method
		if($debug) {
			$this->log->debug(" Validating input form properties");
		}
 
 		// ActionErrors (Aray/HashMap !!!!!!!!!!!!!!!)
 		// Validate the input for this ActionForm (eg: LogonForm)
 		// Note: We validate that a field is present, but not necessarily
 		// correct. Eg: a password is filled, but it may not be the correct
 		// password. We normally verify the input in an Action subclass
 		// such as LogonAction
 		// Note: If we are using phpLIB OOHForms validation, set 
 		//       <action ... validate = "true"> in phpmvc-config.xml
 		$errors = NULL;
		$errors = $form->validate($mapping, $request);

		// Check if the $errors object contains any recorded errors
		$errorsEmpty = True;	// no errors
		if( is_object($errors) ) {
			$errorsEmpty = $errors->isEmpty(); // True if no errors recorded
		}

		if($errors == NULL || $errorsEmpty) {
			// If no $errors object, or $errors object has no errors recorded
			if($trace) {
				$this->log->trace("  No errors detected, accepting input");
			}

			return True;
		}

		// Special handling for multipart request
		if($form->getMultipartRequestHandler() != NULL) {
			if($trace) {
				$this->log->trace("  Rolling back multipart request");
			}

			$mpReqHandler = $form->getMultipartRequestHandler();
			$mpReqHandler->rollback();
		}

		// Has an input form been specified for this mapping?
		$input = $mapping->getInput();	// String
		if($input == NULL) {
			if($trace) {
				$this->log->trace("  Validation failed but no input form available");
			}

			#$response->sendError(HttpServletResponse.SC_INTERNAL_SERVER_ERROR,
			#                      getInternal().getMessage("noInput",
			#                                               mapping.getPath()));

			return False;
		}

		/////
		// Save our form bean and error messages and return to the input form if possible
		if($debug) {
			$this->log->debug(" Validation failed, returning to '".$input."'");
		}

		// The FormBean (As per suggestion by Jan Fetyko)
		$request->setAttribute(Action::getKey('FORM_BEAN_KEY'), $form);

		// The Errors object
		$request->setAttribute(Action::getKey('ERROR_KEY'), $errors);
		if( get_class($request) == 'MultipartRequestWrapper' ) {
			$request = $request->getRequest(); // cast (MultipartRequestWrapper)
		}

		#$uri = $this->appConfig->getPrefix() . $input;	// String
		$uri = $input;	// String
		$this->doForward($uri, $request, $response);
		return False;

	}


	/**
	* Do a forward to specified uri using request dispatcher.
	* This method is used by all internal method needi
	*
	* <p>Credits: Cedric Dumoulin (original jakarta Struts method)
	*
	* @param string				URI (path) or Definition name to forward 
	*  (Eg: '/index.php')
	* @param HttpRequestBase	Current page request
	* @param HttpRequestBase	Current page response
	* @private
	* @returns void
	*
	*/
	function doForward($uri, &$request, &$response) {

		$debug = $this->log->getLog('isDebugEnabled');
		$trace = $this->log->getLog('isTraceEnabled');

		if($trace) {
			$this->log->trace('Start: RequestProcessor->doForward(...)'.
									'['.__LINE__.']');
		}

		$appServerContext = 
				$this->actionServer->appServerConfig->getAppServerContext();
		$actionDispatcher = $appServerContext->getInitParameter('ACTION_DISPATCHER');		
		$ad = new $actionDispatcher; // RequestDispatcher for the application

		if($ad == NULL) {
			#response.sendError(HttpServletResponse.SC_INTERNAL_SERVER_ERROR,
			#                      getInternal().getMessage
			#                      ("requestDispatcher", uri));
			return;
		}

		// Set a reference to the ActionServer instance
		$ad->setActionServer($this->actionServer);

		$ad->forward($uri, $request, $response);

    }


	/**
	* Do an include of specified uri using request dispatcher.
	* This method is used by all internal method needi
	*
	* <p>Credits: Cedric Dumoulin (original Jakarta Struts method)
	*
	* @param string				Uri of page to include
	* @param HttpRequestBase 	Current page request
	* @param HttpResponseBase	Current page response
	* @private
	* @returns void
	*
	*/
	function doInclude($uri, $request, $response) {

		// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!
		$appServerContext = 
				$this->actionServer->appServerConfig->getAppServerContext();
		$rd = $appServerContext->getRequestDispatcher($uri); // RequestDispatcher
		if($rd == NULL) {
			#$response->sendError(HttpServletResponse.SC_INTERNAL_SERVER_ERROR,
         #                      getInternal().getMessage
         #                      ("requestDispatcher", uri));

			return;

		}

		$rd->include($request, $response);
	}


	// ----- Support Methods --------------------------------------------- //

	/**
	* Return the debugging detail level that has been configured for our
	* ActionServer controller
	*
	* @public
	* @returns int
	*/
	function getDebug() {

		return $this->actionServer->getDebug();

	}


	/**
	* Return the <code>MessageResources</code> instance containing our
	* internal message strings.
	*
	* @private
	* @returns MessageResources
	*/
	function getInternal() {

		return $this->actionServer->getInternal();

	}


	/**
	* Return the ServletContext for the web application we are running in.
	*
	* @private
	* @returns AppServerContext
	*/
	function getServletContext() {

		$appServerContext = 
				$this->actionServer->appServerConfig->getAppServerContext();
		return $appServerContext;

	}

	/**
	* Log the specified message to the servlet context log for this
	* web application.
	*
	* @param string		The message to be logged
	* @private
	* @returns void
	*/
	#function log($message) {
	#
	#	$actionServer->log($message);
	#
	#}


	/**
	* Log the specified message and exception to the servlet context log
	* for this web application.
	*
	* @param string		The message to be logged
	* @param exception	The exception to be logged
	* @private
	* @returns void
	*/
	function log($message, $exception='') {

        $actionServer->log($message, $exception);

    }

}
?>