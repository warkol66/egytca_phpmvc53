<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/classes/phpmvc/action/ActionServer.php,v 1.16 2006/02/22 06:57:25 who Exp $
* $Revision: 1.16 $
* $Date: 2006/02/22 06:57:25 $
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
* <p><strong>ActionServer</strong> represents the "controller" in the
* Model-View-Controller (MVC) design pattern for web applications that is
* commonly known as "Model 2".  This nomenclature originated with a
* description in the JavaServerPages Specification, version 0.92, and has
* persisted ever since (in the absence of a better name).</p>
*
* <p>Generally, a "Model 2" application is architected as follows:</p>
* <ul>
* <li>The user interface will generally be created with PHP pages, which
*     will not themselves contain any business logic.  These pages represent
*     the "view" component of an MVC architecture.</li>
* <li>Forms and hyperlinks in the user interface that require business logic
*     to be executed will be submitted to a request URI that is mapped to the
*     controller ActionServer.</li>
* <li>There will be one instance of this servlet class,
*     which receives and processes all requests that change the state of
*     a user's interaction with the application.  This component represents
*     the "controller" component of an MVC architecture.</li>
* <li>The controller ActionServer will select and invoke an action class to perform
*     the requested business logic.</li>
* <li>The action classes will manipulate the state of the application's
*     interaction with the user, typically by creating or modifying Actions
*     that are stored as request or session attributes (depending on how long
*     they need to be available).  Such Actions represent the "model"
*     component of an MVC architecture.</li>
* <li>Instead of producing the next page of the user interface directly,
*     action classes will generally use the
*     <code>ActionDispatcher->forward(...)</code> facility to pass control
*      to an appropriate PHP page to produce the next page of the user
*      interface.</li>
* </ul>
*
* <p>The standard version of <code>ActionServlet</code> implements the
*    following logic for each incoming HTTP request.  You can override
*    some or all of this functionality by subclassing this class and
*    implementing your own version of the processing.</p>
* <ul>
* <li>Identify, from the incoming request URI, the substring that will be
*     used to select an action procedure.</li>
* <li>Use this substring to map to the PHP class name of the corresponding
*     action class (an implementation of the <code>Action</code> interface).
*     </li>
* <li>Optionally populate the properties of an <code>ActionForm</code> bean
*     associated with this mapping.</li>
* <li>Call the <code>perform()</code> method of this action class, passing
*     on a reference to the mapping that was used (thereby providing access
*     to the underlying ActionServer and AppServerContext, as well as any
*     specialized properties of the mapping itself), and the request and
*     response that were passed to the controller by the Web server.
*     </li>
* </ul>
*
* @author John C Wildenauer (php.MVC port)<br>
*  Credits:<br>
*  Craig R. McClanahan (Jakata Struts ActionServlet)
*  Ted Husted (Jakata Struts ActionServlet)
* @version $Revision: 1.16 $
* @public
*/
class ActionServer extends HttpAppServer {

	// ----- Instance Variables --------------------------------------------- //

	/**
	* Context-relative path to the php.MVC configuration file
	* @type string
	*/
	var $configPath = './WEB-INF/phpmvc-config.xml';

	/**
	* Digester object to process the xml configuration file
	* @type Digester
	*/
	var $digester = NULL;

	/**
	* XML parser case folding - False leaves xml element case unchanged
	* Default is True (uppercase xml elements)
	* @type boolean
	*/
	var $parserCaseFolding = False;

	/**
	* Database sources
	* @type array
	*/
	var $dataSources = array();	// HashMap

	/**
	* Debug level detail for this php.MVC Web application
	* @type int
	*/
	var $debug = 0;

	/**
	* Debugging detail level for xml configuration file parsing
	* @type int
	*/
	var $detail = 0;

	/**
	* The internal resources object
	* @type MessageResources
	*/
	var $internalRes = NULL;
	
	/**
	* The Class base name of our internal resources.
	* @type string
	*/
	var $internalResName = 'ActionResources';

	/**
	* Logging class
	* @type Log
	*/
	var $log = NULL;

	/**
	* RequestProcessor class handles request processing logic
	* @type RequestProcessor
	*/
	var $processor = NULL;

	/**
	* Use a validating XML parser to read the xml configuration file(s)
	* @type boolean
	*/
	var $validating = False;

	/**
	* Stores startup and global configuration information for an php.MVC Web
	* application, and makes this information available to the application 
	* instance.
	* @type AppServerConfig
	*/
	var $appServerConfig = NULL;
	
	/**
	* Store references to the PlugIn class instances.
	*
	* <p>Applications can retrieve a PlugIn reference from the ActionServer:<br>
	*  <code>actionServer->getPlugIn($plugInKey)</code>.</p>
	*
	* @type array
	*/	
	var $plugIns	= array();	// array of PlugIn object references	


	/**
	* Set the path to the application xml configuration file.
	* 
	* @param string	The relative path to the application xml configuration file.
	*						Something like: './WEB-INF/phpmvc-config.xml'.
	* @public
	* @returns void
	*/
	function setConfigPath($configPath) {
		$this->configPath = $configPath;
	}


	// ----- Constructors --------------------------------------------------- //

	function ActionServer () {

		$this->log	= new PhpMVC_Log();
		$this->log->setLog('isDebugEnabled'	, False);
		$this->log->setLog('isTraceEnabled'	, False);

	}


	// ----- HTTP Methods --------------------------------------------------- //
	
	/**
	* Shut down the php.MVC Server, releasing allocated resources
	* 
	* @public
	* @returns void
	*/
	function destroy() {
		
		$debug = $this->log->getLog('isDebugEnabled'); 
  		if($debug) {
  			$this->log->debug(" Finalising ...");	// Internal message !!
  		}
		
		$this->destroyApplications();	// terminate php.MVC application components
		$this->destroyDataSources();
		#$this->destroyApplicationConfig();	// !!
		#$this->destroyMessageResources();	// !!
		#$this->destroyInternal();				// !!
		$appContext = AppServer::getAppserverContext(); 	// !!
		$appContext->removeAttribute(Action::getKey('ACTION_SERVER_KEY'));

	}


	/**
	* Initialize the application.
	*
	* @public
	* @returns void
	*/
	function init(&$appServerConfig) {

		// Set the AppServerConfig object for this Web application
		$this->appServerConfig = $appServerConfig;

		$this->initInternal();		// setup internal message resources
		$this->initOther();			// setup other global controller stuff
		$this->initServlet();		// setup application mapping

		// Initialize sub-applications as needed
		// .......

		// Setup and call the XML configuration info digester
		// Class: ApplicationConfig
		$applConfig = $this->initApplicationConfig('', $this->configPath);

		$this->initApplicationMessageResources($applConfig);
		$this->initApplicationDataSources($applConfig);
		$this->initApplicationPlugIns($applConfig);

		// Setup sub-applications 
		// [later date]

		
		$this->destroyConfigDigester();
		
		// Return the ApplicationConfig object
		return $applConfig;
    }


	/**
	* Handle an HTTP GET request.
	*
	* @param HttpRequestBase The HTTP client request we are processing
	* @param HttpResponseBase The HTTP response we are creating for the client
	* @param HTTP_GET_VARS The server vars
	* @public
	* @returns void
	*/
	function doGet(&$request, &$response, $GET='') {

		if($GET == '') {
			$GET = $_GET;
		}

		$request->setMethod('GET');

		// Save any POST data also.
		// Note: This became an issue in php5 with form POST requests (
		// (GET data remains in the request parameters with form POST request)
		$this->doPost($request, $response, '', '', $GET);

	}


	/**
	* Handle an HTTP POST request.
	*
	* @param request			The HTTP client request we are processing
	* @param response			The HTTP response we are creating for the client
	* @param HTTP_POST_VARS	The server POST variables
	* @param HTTP_POST_FILES The server File upload vars
	* @param HTTP_GET_VARS  The server GET variables
	* @public
	* @returns void
	*/
	function doPost(&$request, &$response, $POST='', $FILES='', $GET='') {

		if($POST == '') {
			$POST = $_POST;
		}
		if($FILES == '') {
			$FILES = $_FILES;
		}
		if($GET == '') {
			$GET = $_GET;
		}

		$request->setMethod('POST');
		$request->setPostVars($POST);
		$request->setFilesVars($FILES);
		$request->setGetVars($GET);

		$this->process($request, $response);

	}


	// ----- Public Methods ------------------------------------------------- //

	/**
	* Return the debugging level for this application.
	*
	* @public
	* @returns int
	*/
	function getDebug() {

		return $this->debug;

	}


	/**
	* Return the <code>MessageResources</code> instance containing our
	* internal message strings.
	*
	* @public
	* @returns MessageResources
	*/
	function getInternalRes() {

		return ($this->internalRes);

    }


	/**
	* Log the message if the debugging level for this application
	* is equal or greater than the message debug detail level.
	*
	* @param Message	The message to be logged
	* @param int		The debugging detail level of this message
	* @public
	* @returns void
	*/
	function log($message, $level) {

		if($this->debug >= $level) {
			$this->log($message);	// call super class !!
		}

	}


	/**
	* Set the AppServerConfig object for this Web application
	*
	* <p>(SEE init(config)
	*
	* @param object The AppServerConfig object (reference)
	* @public
	* @returns void
	*/
	#function setAppServerConfig(&$config) {
	#
	#	$this->$appServerConfig = $config;
	#
	#}


	/**
	* Get dataSource object
	*
	* <p>Returns a reference to the data source object if found, otherwise
	* returns NULL.
	*
	* <p>Note:<br>
	* Open a connection to the requested datasource on the first call to open().
	* The datasource open() method should return without further action on
	* subsequent calls.
	*
	* @param string The dataSourceKey. Eg: 'PEAR_MYSQL_DATA_SOURCE'
	* 
	* @public
	* @returns object
	*/
	function &getDataSource($dataSourceKey) {

		// Select a datasource. There could be several datasources available.
		// If we have only one datadource, we could have used the default datasource
		// key: 'phpmvc.action.DATA_SOURCE'. See Action::getKey($key)
		if( array_key_exists($dataSourceKey, $this->dataSources) ) {

			$db =& $this->dataSources[$dataSourceKey];

			// If open() was previously called, it should just return. No problem.
			$db->open();
		
			return $db;

		} else {
			$debug = $this->log->getLog('isDebugEnabled');
			if($debug) {
				$this->log->debug('No datasource found: ActionServer->getDataSource(...)'.
										'['.__LINE__.']');
			}

			// Throw an error.
			$ret = NULL; // Php5 (#63): Only variable references should be returned by reference 
			return $ret;

		}

	}


	/**
	* Get a PlugIn object
	*
	* <p>Returns a reference to the PlugIn object if found, otherwise
	* returns NULL.</p>
	*
	* <p>Note:<br>
	* The PlugIn init() method is called to perform any initialisation required.
	* Note: init() returns immediately if the getPlugIn() method has already
	* been called. Eg: PlugIn initialisation is complete.</p>
	*
	* @param string The plugInKey. Eg: 'SMARTY_PLUGIN'
	*
	* @public
	* @returns object
	*/
	function &getPlugIn($plugInKey) {

		// Note: 1) The 'config' variable represents the ApplicationConfiguration
		//          object. It is a placeholder for future reference and is not 
		//          currently used..
		//       2) The cleanest way to implement 'config' will be to add an
		//          "appConfig" property to the ActionServer class, along with
		//          a setter and getter. [(s/g)etAppConfig($appConfig)]. The 
		//          setter would be placed in ActionServer->process(...), and
		//          the getter can then be called here.
		$config = NULL;


		if( array_key_exists($plugInKey, $this->plugIns) ) {

			$plugIn =& $this->plugIns[$plugInKey];

			// Initialise the PlugIn object.
			// Note: init() returns immediately if the getPlugIn() method has
			// already been called. Eg: PlugIn initialisation is complete.
			$plugIn->init($config);

			return $plugIn->plugIn;

		} else {
			$debug = $this->log->getLog('isDebugEnabled');
			if($debug) {
				$this->log->debug('No plugin found: ActionServer->getPlugIn('.$plugInKey.')'.
										'['.__LINE__.']');
			}

			// Throw an error.
			$ret = NULL; // Php5 (#63): Only variable references should be returned by reference 
			return $ret;

		}
	}


	// ----- Protected Methods ---------------------------------------------- //

	/**
	* Terminate sub-applications associated with this application (if any).
	* <p>Later-date
	*
	* @private
	* @returns void
	*/
	function destroyApplications() {

		// Later-date
		return NULL;

	}


	/**
	* Release the configDigester resources
	*
	* @private
	* @returns void
	*/
	function destroyConfigDigester() {

		$this->digester = NULL;

	}


	/**
	* Release the internal MessageResources resources
	*
	* @private
	* @returns void
	*/
	function destroyInternal() {

		$this->internalRes = NULL;

	}


	/**
	* Return the application configuration object for the currently selected
	* sub-application.
	*
	* @param HttpRequestBase The HTTP request we are processing
	* @private
	* @returns ApplicationConfig
	*/
	function getApplicationConfig($request) {

		// Get the application configuration object Eg: ApplicationConfig
		$appConfig = $request->getAttribute(Action::getKey('APPLICATION_KEY'));

		// 
		if($appConfig == NULL) { 
			#$appContext = AppServer::getAppserverContext(); 	// !!
			$appContext = $this->appServerConfig->getAppServerContext();
			$appConfig = $appContext->getAttribute(Action::getKey('APPLICATION_KEY'));
		}

		return $appConfig;

	}


	/**
	* <p>Initialize the application configuration information for the
	* specified sub-application.</p>
	*
	* @param string	Application prefix for this application
	* @param string	Context-relative resource path for this application's
	*  configuration resource (xml file)
	*
	* @private
	* @returns ApplicationConfig
	*/
	function initApplicationConfig($prefix=NULL, $configPath) {

		$debug = $this->log->getLog('isDebugEnabled'); 
  		if($debug) {
  			$this->log->debug("Initializing application path '".$prefix.
  				"' configuration from '".$configPath."'");
  		}

		// Parse the application configuration (xml) for this application
		$applConfig	= NULL;	// ApplicationConfig root config object
		$mapping		= NULL;	// String


		// The root application config object
		$applConfig = new ApplicationConfig($prefix, $this);

      // Support for application-wide ActionMapping override
		$mapping = $this->appServerConfig->getInitParameter('mapping');
		if($mapping != NULL) {
			$applConfig->setActionMappingClass($mapping);
		}

		$this->initConfigDigester();	// setup a new xml digester


		// Push the root config object onto the top of the Digester stack
		$this->digester->push($applConfig);
		$applConfig	= $this->digester->parse($configPath);

		$appContext = $this->appServerConfig->getAppServerContext(); 	// !!
		$appContext->setAttribute
					(Action::getKey('APPLICATION_KEY').$prefix, $applConfig); 
 

		// Return the completed configuration object
		$applConfig->freeze();
		return $applConfig;

	}


	/**
	* <p>Initialize the application data sources for the specified
	* sub-application.</p>
	*
	* @param ApplicationConfig The ApplicationConfig information for this
	*  application
	* @private
	* @returns void
	*/
	function initApplicationDataSources($config) {

		$debug = $this->log->getLog('isDebugEnabled');
		$trace = $this->log->getLog('isTraceEnabled');

		if($trace) {
			$this->log->trace('Start: ActionServer->initApplicationDataSources(...)'.
									'['.__LINE__.']');
		}

  		if($debug) {
  			$this->log->debug(" ActionServer->initApplicationDataSources()[".
  										__LINE__."]: Initializing application path: '".
  										$config->getPrefix()."' data sources.");
  		}

      $dataSources = array();	// DataSourceConfig array()
		$dataSources = $config->findDataSourceConfigs(); // database config files
		if($dataSources == NULL) {
			$dataSources[0] = new DataSourceConfig;	// DataSourceConfig[0] !!!
		}

		foreach($dataSources as $dataSource) { // each dataSource config

			$error = NULL; // catch errors TO-DO

	  		if($debug) {
	  			$this->log->debug("  ActionServer->initApplicationDataSources()[".
	  									__LINE__."]: Initializing application path: '".
	  						$config->getPrefix()."' DataSourceConfig: '".get_class($dataSource)."'");
	  		}

			$oDataSource = NULL;	// holds an actual  dataSource object

       	// Build the DataSource object from the DataSourceConfig object
			$dataSourceType = $dataSource->getType(); // eg: BasicDataSource
			$oDataSource = new $dataSourceType;

			$beanUtils = new PhpBeanUtils;
			$beanUtils->populate($oDataSource, $dataSource->getProperties());

			// Open a connection to the data source:
			// Not here. The db reference will be lost in the application caching.
			// Retrieve a reference to the datasource from any class that has access
			// to ActionServer. Eg: MyCartAction (extends Action).


			//$oDataSource->setLogWriter(scw);	// Setup ServletContextWriter !!!

			// Catch
  			if($error) {

  				$msg = $this->internalRes->getMessage("dataSource->init", 
  															$dataSource->getKey());
  				$this->log->error($msg, $error);

  				#return $this->internalRes->getMessage("dataSource.init", // !!!
  				#										$dataSource->getKey());
  			}


         // Add the DS key=>value pair to the ApplicationContext
			$appContext = $this->appServerConfig->getAppserverContext();
			$appContext->setAttribute($dataSource->getKey(), $oDataSource);

			// Add the DS object to $this->dataSources HashMAp
			// Note: Do not save the $oDataSource as a reference (=&)
			$this->dataSources[$dataSource->getKey()] = $oDataSource;

		}	// foreach(...)

	}


	/**
	* Initialize the plug ins for the specified sub-application.
	*
	* <p>PlugIn objects are created and configured in a similar manner to a standard Rule
	* by the configuration Digester ObjectCreateRule object.<br>
	* The PlugIns are stored to the ApplicationConfig object and can be retrieved here.</p>
	*
	* <p>A reference to the PlugIn object is saved to the ActionServer->plugIns array keyed
	* by the PlugIn key value.</p>
	*
	*<p>Retrieve a reference to a PlugIn like this:<br>
	* $myPlugIn =& $this->actionServer->getPlugIn("MY_PLUGIN_KEY");</p>
	*
	* @param ApplicationConfig The ApplicationConfig information for this application
	*  
	* @private
	* @returns void
	*/
	function initApplicationPlugIns($config) {

		$debug = $this->log->getLog('isDebugEnabled'); 
  		if($debug) {
  			$this->log->debug("Initializing application path '".
  									$config->getPrefix()."' plug ins");
  		}


		// Note: The PlugIn instance is created (new MyPlugIn) in Digester/ObjectCreateRule
		$plugIns = array();	// PlugIn
		$plugIns = $config->findPlugIns();
		if($plugIns == NULL) {
			return 'No PlugIns loaded'; // Catch
		}


		// Revision by Erwan le Gall 24-Sep-2004. Saving the PlugIn class as a reference ("=&")
		// instead of saving a copy of the PlugIn class.
		foreach($plugIns as $idx => $plugIn) {
			$keyStr = $plugIn->getKey(); // Eg: 'SMARTY_PLUGIN_KEY'
			// Bug report by Tom Howard - pass-by-reference ("=&") causes problems with
			// multiple PlugIn instances. Remove the pass-by-reference ("=&") [Revised per Erwan le Gall]
			$this->plugIns[$keyStr] =& $plugIns[$idx];
		}

	}


	/**
	* <p>Initialize the application MessageResources for the specified
	* sub-application.</p>
	*
	* @param ApplicationConfig The ApplicationConfig information for this 
	*  application
	* @private
	* @returns void
	*/
	function initApplicationMessageResources($config) {

		$error = NULL; // catch errors TO-DO

		$msgResrs = array();	// MessageResourcesConfig 
		$msgResrs = $config->findMessageResourcesConfigs();

		if($msgResrs == NULL) {
			return 'No MessageResourcesConfigs found.';
		}	

		foreach($msgResrs as $msgRes) {

			if(($msgRes->getFactory() == NULL)||($msgRes->getParameter() == NULL)) {
				continue;
			}

			$debug = $this->log->getLog('isDebugEnabled'); 
	  		if($debug) {
	  			$this->log->debug("Initializing application path '".$config->getPrefix().
	  									"' message resources from '".$msgRes->getParameter()."'");
  			}

			$factory = $msgRes->getFactory();	// String
			MessageResourcesFactory::setFactoryClass($factory);	// static property !!! 
			// MessageResourcesFactory  
			$factoryObject = MessageResourcesFactory::createFactory();    
			$resources = $factoryObject->createResources($msgRes->getParameter());
			// Return NULL or error for unknown Locale
			$resources->setReturnNull($msgRes->getNull());
			$appContext = AppServer::getAppserverContext(); 	// !!
			$appContext->setAttribute($msgRes->getKey().$config->getPrefix(), $resources); 
          
			// Catch
			if($error) {
				
				$msg = $this->internalRes->getMessage("applicationResources",
										$msgRes->getParameter());
	  			$this->log->error($msg, $error);
	  			#return $this->internalRes->getMessage("applicationResources", // !!!
	  			#							$msgRes->getParameter()) ;
			}

		}	// foreach()

	}


	/**
	* Setup an Digester XML application configuration processor
	* <p>Create (if needed) and return a new Digester instance that has been
	* initialized to process php.MVC application configuraiton files and
	* configure a corresponding ApplicationConfig object (which must be
	* pushed on to the evaluation stack before parsing begins).</p>
	*
	* @private
	* @returns Digester
	*/
	function initConfigDigester() {

		// Do we have an existing instance?
		if($this->digester != NULL) {
			return $this->digester;
		}

		// Create and return a new Digester instance
		$digester = new Digester();
		#$this->log->setLog('isDebugEnabled', 1); // see also: Digester constructor

		// Setup Digester behavour
		#$digester->setProperty('log', $this->log);				// ref
		#$digester->setProperty('saxLog', $this->saxLog);		// ref
		#$digester->setProperty('namespaceAware', 'False'); // NS not yet supported
		$digester->setValidating($this->validating);
		#$digester->setUseContextClassLoader(True);	// !!!

		// XML parser case folding - False leaves case unchanged
		$digester->parserSetOption(XML_OPTION_CASE_FOLDING, $this->parserCaseFolding);
		
		// Set of config rules. See: WEB-INF/classes/phpmvc/config/ConfigRuleSet.php
		$digester->addRuleSet(new ConfigRuleSet());	
		// Set of DTD locations
		#foreach($this->registrations as $registration) {
		#	// refer Struts source
		#	// ...
		#}

		$this->digester = $digester;

	}


	/**
	* Initialize our internal MessageResources bundle.
	*
	* @private
	* @returns void
	*/
	function initInternal() {

		$error = NULL; // catch errors TO-DO

		// Message Factory call
		#$this->internalRes = 
		#			MessageResources::getMessageResources($this->internalResName);

		// Catch
		// FIX SETUP ERROR HANDLING
		return; // !!!!!!!!!!!!!!
		
		$error = 'in ActionServer->initInternal()';
		if($this->internalRes == NULL) {
			// TO-DO CATCH ERRORS
			$this->log->error("Cannot load internal resources from '" .
									$this->internalResName . "'", $error);

			# Throw	// !!!  

		}

	}


	/**
	* [Configurations from the AppServer Context]
	* Initialize other global characteristics of the controller.
	* <p>To-do
	*
	* @private
	* @returns void
	*/
	function initOther() {

		$error = NULL; // catch errors TO-DO
		$value = NULL;	// String
		
		// Setup "config" parameter (Eg: "/WEB-INF/struts-config.xml")
		// To-do

	}


	/**
	* Initialize the application mapping under which our controller
	* is being accessed....
	*
	* SEE: initApplicationConfig() above !!!
	*/
	function initServlet() {

		// Setup php.MVC URL pattern (paths) using 
		//  initApplicationConfig() above !!

	}


	/**
	* Perform the standard request processing for this request, and create
	* the corresponding response.
	*
	* @param HttpRequestBase	The server request we are processing
	* @param HttpResponseBase	The server response we are creating
	*
	* @private
	* @returns void
	*/
	function process($request, $response) {

		// Select the sub-application to which the request belongs

		$appContext = $this->appServerConfig->getAppServerContext();

		$reqUtils = new RequestUtils;
		$reqUtils->selectApplication($request, $appContext);

		// Process the HttpRequest and create the corresponding HttpResponse

		// Get the ApplicationConfig object
		$appConfig = $this->getApplicationConfig($request);

		// Get a RequestProcessor instance and process the request
		$requestProcessor = $appConfig->getProcessor();	// "../action/RequestProcessor"
		$requestProcessor->process($request, $response);

	}

}
?>