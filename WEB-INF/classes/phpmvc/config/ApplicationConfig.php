<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/classes/phpmvc/config/ApplicationConfig.php,v 1.11 2006/02/22 07:03:28 who Exp $
* $Revision: 1.11 $
* $Date: 2006/02/22 07:03:28 $
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
* <p>The collection of static configuration information that describes a
* php.MVC-based application or sub-application.  Multiple sub-applications
* are identified by a <em>prefix</em> at the beginning of the context
* relative portion of the request URI.  If no application prefix can be
* matched, the default configuration (with a prefix equal to a zero-length
* string) is selected</p>
*
* @author John C Wildenauer (php.MVC port)<br>
*  Credits:<br>
*  Craig R. McClanahan (original Struts class: see jakarta.apache.org)
* @version $Revision: 1.11 $
* @public
*/
class ApplicationConfig {

	// ----- Class Serialisation ID ----------------------------------------- //

	/** JCW
	* Serialize version info. This is to ensure that the serialized
	* php.MVC configuration data stored on disk is compatable with
	* this config class.
	* Update this info if making changes to the config classes that
	* would be incompatable with older versions
	*
	* <p>Returns a serial string, something like:
	* "$className:$fileName:$versionID"
	* Eg: eg: 'ApplicationConfig:ApplicationConfig.php:20021025-0955'
	*
	* @private
	* @type string
	*/
	var $classID = '';




	// ----- Protected Instance Variables ----------------------------------- //

	/**
	* The set of action configurations for this application, if any,
	* keyed by the <code>path</code> property.
	* @private
	* @type array
	*/
	var $actionConfigs = array();	// HashMap


	/**
	* The set of JDBC data source configurations for this
	* application, if any, keyed by the <code>key</code> property.
	* @private
	* @type array
	*/
	var $dataSources = array();	// HashMap


	/**
	* The set of exception handling configurations for this
	* application, if any, keyed by the <code>type</code> property.
	* @private
	* @type array
	*/
	var $exceptions = NULL;


	/**
	* The set of form bean configurations for this application, if any,
	* keyed by the <code>name</code> property.
	* @private
	* @type array
	*/
	var $formBeans = array();


	/**
	* The set of global forward configurations for this application, if any,
	* keyed by the <code>name</code> property.
	* @private
	* @type array
	*/
	var $forwards = NULL;


	/**
	* The set of message resources configurations for this
	* application, if any, keyed by the <code>key</code> property.
	* @private
	* @type array
	*/
	var $messageResources = NULL;


	/**
	* The set of configured plug in modules for this application,
	* if any, in the order they were declared and configured.
	* @private
	* @type array
	*/
	var $plugIns = NULL;


 	/**
	* The View resources configuration for this application, if any.
	* @private
	* @type array
	*/
	var $viewResourcesConfig = NULL;


	// ----- Protected Properties ------------------------------------------- //

	/**
	* Has this application been completely configured yet?  Once this flag
	* has been set, any attempt to modify the configuration will return an
	* IllegalStateException.
	*
	* @private
	* @type boolean
	*/
	var $configured = False;

	/**
	* The controller configuration object for this application.
	*
	* @private
	* @type ControllerConfig
	*/
	var $controllerConfig = NULL;

	/**
	* The prefix of the context-relative portion of the request URI, used to
	* select this configuration versus others supported by the controller
	* servlet.  A configuration with a prefix of a zero-length String is the
	* default configuration for this web application.
	*
	* @private
	* @type string
     */
	var $prefix = NULL;

	/**
	* The initialized RequestProcessor instance to be used for processing
	* requests for this application.
	*
	* @private
	* @type RequestProcessor
	*/
	var $reqProcessor = NULL;

	/**
	* The <code>ActionServer</code> instance that is managing this
	* application.
	*
	* @private
	* @type ActionServer
	*/
	var $actionServer = NULL;

	/**
	* The default class name to be used when creating action mapping
	* instances.
	*
	* @private
	* @type string
	*/
	var $actionMappingClass = 'ActionMapping';


	/**
	* @public
	* @returns boolean
	*/
	function getConfigured() {
		return $this->configured;
	}


	/**
	* @public
	* @returns ControllerConfig
	*/
	function &getControllerConfig() {
		if($this->controllerConfig == NULL) {
			$this->controllerConfig =& new ControllerConfig();
		}
		$controllerConfig =& $this->controllerConfig;
		return $controllerConfig;
	}

	/**
	* @param ControllerConfig
	* @public
	* @returns void
	*/
	function setControllerConfig($controllerConfig) {
		if( $this->getConfigured() ) {
			#throw new IllegalStateException("Configuration is frozen");
			return;
		}
		$this->controllerConfig = $controllerConfig;
	}


	/**
	* @public
	* @returns ViewResourcesConfig
	*/
	function &getViewResourcesConfig() {
		if($this->viewResourcesConfig == NULL) {
			$this->viewResourcesConfig =& new ViewResourcesConfig();
		}
		return $this->viewResourcesConfig;
	}

	/**
	* @param ViewResourcesConfig
	* @public
	* @returns void
	*/
	function setViewResourcesConfig(&$viewResourcesConfig) {
		if($this->getConfigured()) {
			return "Configuration is frozen";
		}
		$this->viewResourcesConfig =& $viewResourcesConfig;
	}


	/**
	* @public
	* @returns string
	*/
	function getPrefix() {
		return $this->prefix;
	}


	/**
	* @public
	* @returns RequestProcessor
	*/
	function getProcessor() {

		if($this->reqProcessor == NULL) {

         $controllerConfig = $this->getControllerConfig();
         
         // Get the corrent request processor class. Eg: 'RequestProcessor'
         $reqProcessorClass = $controllerConfig->getProcessorClass();
       
         $this->reqProcessor = new $reqProcessorClass;	// !!!
         
			$this->reqProcessor->init($this->actionServer, $this);
				
			// Catch
				#throw new UnavailableException
				#	("Cannot initialize RequestProcessor of class " +
				#	getControllerConfig().getProcessorClass() + ": " + t);

		}

		return $this->reqProcessor;

	}


	/**
	* @public
	* @returns ActionServer
	*/
	function getActionServer() {
		return $this->actionServer;
	}


	/**
	* @public
	* @returns string
	*/
	function getActionMappingClass() {
		return $this->actionMappingClass;
	}

	/**
	* @param string The ActionMapping
	* @public
	* @returns void
	*/
	function setActionMappingClass($actionMappingClass) {
		$this->actionMappingClass = $actionMappingClass;
	}


	// ----- Constructors --------------------------------------------------- //

	/**
	* Construct an ApplicationConfig object according to the specified
	* parameter values.
	*
	* @param string The context-relative URI prefix for this application
	* @param ActionServer The ActionServer that is managing this application
	*/
	function ApplicationConfig($prefix=NULL, &$actionServer) {

		$this->prefix			= $prefix;
		$this->actionServer	=& $actionServer;

		// Set the class version info for serialisation
		$this->classID = $this->_getClassID();

    }


	// ----- Public Methods ------------------------------------------------- //

	/**
	* The application context [page|request|session] !!!
	*
	* @public
	* @returns string
	*/
	function getAppContext() {
	
		;

	}

	/**
	* Add a new <code>ActionConfig</code> instance to the set associated
	* with this application.
	*
	* @param ActionConfig The new configuration instance to be added
	* @public
	* @returns void
	*/
	function addActionConfig($config) {

		if($this->getConfigured()) {
			#throw new IllegalStateException("Configuration is frozen");
			return;
		}

		$config->setApplicationConfig($this);
		$path = $config->getPath();
		$this->actionConfigs[$path]= $config;

	}


	/**
	* Add a new <code>DataSourceConfig</code> instance to the set associated
	* with this application.
	* Usage:
	*	class phpmvc/config/ConfigRuleSet
	*		....
	*		addSetNext($setNexyPattern, 'addDataSourceConfig');
	*		....
	*
	* @param DataSourceConfig The new configuration instance to be added
	* @public
	* @returns void
	*/
	function addDataSourceConfig($config) {

		if($this->getConfigured()) {
			#throw new IllegalStateException("Configuration is frozen");
			return;
		}

		$key = $config->getKey();
		$this->dataSources[$key]= $config;

	}


	/**
	* Add a new <code>ExceptionConfig</code> instance to the set associated
	* with this application.
	*
	* @param ExceptionConfig The new configuration instance to be added
	* @public
	* @returns void
	*/
	function addExceptionConfig($config) {

		if($this->getConfigured()) {
			#throw new IllegalStateException("Configuration is frozen");
			return;
		}
		$this->exceptions->put($config->getType(), $config);

	}


	/**
	* Add a new <code>FormBeanConfig</code> instance to the set associated
	* with this application.
	*
	* @param FormBeanConfig The new configuration instance to be added
	* @public
	* @returns void
	*/
	function addFormBeanConfig($config) {

		if($this->getConfigured()) {
			#throw new IllegalStateException("Configuration is frozen");
			return "Configuration is frozen";
		}

		$this->formBeans[$config->getName()] = $config;

	}


	/**
	* Add a new <code>ForwardConfig</code> instance to the set of global
	* forwards associated with this application.
	*
	* @param ForwardConfig The new configuration instance to be added
	* @public
	* @returns void
	*/
	function addForwardConfig($config) {

		if($this->getConfigured()) {
			#throw new IllegalStateException("Configuration is frozen");
			return;
		}
		$this->forwards->put($config->getName(), $config);

	}

	/**
	* Add a new <code>MessageResourcesConfig</code> instance to the set
	* associated with this application.
	*
	* @param MessageResourcesConfig The new configuration instance to be added
	*
	* @public
	* @returns void
	*/
	function addMessageResourcesConfig($config) {

		if($this->getConfigured()) {
			#throw new IllegalStateException("Configuration is frozen");
			return;
		}
		$this->messageResources->put($config->getKey(), $config);

	}


	/**
	* Add a newly configured PlugIn instance to the set of
	* plug in modules for this application.
	*
	* @param PlugIn The new configured plugIn module
	* @public
	* @returns void
	*/
	function addPlugIn($plugIn) {

		if($this->getConfigured()) {
			#throw new IllegalStateException("Configuration is frozen");
			return;
		}
		$this->plugIns[] = $plugIn;

	}


	/**
	* Return the action configuration for the specified path, if any;
	* otherwise return <code>null</code>.
	*
	* @param string The path of the action configuration to return
	* @public
	* @returns ActionConfig
	*/
	function findActionConfig($path) {

		if($this->actionConfigs == NULL) { // Array (HashMap)
			return NULL;
		}

		$actionConfig = NULL;
		if( array_key_exists($path, $this->actionConfigs) ) {
			$actionConfig = $this->actionConfigs[$path];	// ActionConfig
		}

		return $actionConfig;

    }


	/**
	* Return the action configurations (ActionConfig[]) for this application.
	* If there are none, a zero-length array is returned.
	* 
	* @public
	* @returns array
	*/
	function &findActionConfigs() {

		if($this->actionConfigs == NULL) {
			$ret = NULL; // Php5 (#63): Only variable references should be returned by reference 
			return $ret;
		}

		$actionConfigs = array();
		$actionConfigs =& $this->actionConfigs;
		return $actionConfigs;

    }


	/**
	* Return the data source configuration for the specified key, if any;
	* otherwise return <code>null</code>.
	*
	* @param string The key of the data source configuration to return
	* @public
	* @returns DataSourceConfig
	*/
	function findDataSourceConfig($key) {

		if($this->dataSources == NULL) {
			return NULL;
		}

		return $this->dataSources[$key];	// DataSourceConfig

	}


	/**
	* Return the data source configurations (DataSourceConfig[]) for this 
	* application. If there are none, a zero-length array is returned.
	*
	* @public
	* @returns array
	*/
	function &findDataSourceConfigs() {

		if($this->dataSources == NULL) {
			$ret = NULL; // Php5 (#63): Only variable references should be returned by reference 
			return $ret;
		}

		$dataSources = array();
		$dataSources =& $this->dataSources;
		return $dataSources;

   }


	/**
	* Return the exception configuration for the specified type, if any;
	* otherwise return <code>null</code>.
	*
	* @param string The exception class name to find a configuration for
	* @public
	* @returns ExceptionConfig
	*/
	function findExceptionConfig($type) {

		if($this->exceptions == NULL) {
			return NULL;
		}

		return $this->exceptions->get($type);	// ExceptionConfig

	}


	/**
	* Return the exception configurations (ExceptionConfig[]) for this 
	* application.  If there are none, a zero-length array is returned.
	*
	* @public
	* @returns array
	*/
	function findExceptionConfigs() {

		if($this->exceptions == NULL) {
			return NULL;
		}

		$results = $this->exceptions->values();	// HashMap
		return $results;	// !!!!!

	}


	/**
	* Return the form bean configuration for the specified key, if any;
	* otherwise return <code>null</code>.
	*
	* @param string The name of the form bean configuration to return
	* @public
	* @returns FormBeanConfig
	*/
	function findFormBeanConfig($name) {

		if($this->formBeans == NULL || $name == NULL) {
			return NULL;
		}

		if( array_key_exists($name, $this->formBeans) ) {
			return $this->formBeans[$name];
		} else {
			return NULL;
		}

	}


	/**
	* Return the form bean configurations (FormBeanConfig[]) for this 
	* application.  If there are none, a zero-length array is returned.
	*
	* @public
	* @returns array
	*/
	function &findFormBeanConfigs() {

		if($this->formBeans == NULL) {
			$ret = NULL; // Php5 (#63): Only variable references should be returned by reference 
			return $ret;
		}

		$formBeans = array();
		$formBeans =& $this->formBeans;
		return $formBeans;

	}


	/**
	* Return the forward configuration for the specified key, if any;
	* otherwise return <code>null</code>.
	*
	* @param string The name of the forward configuration to return
	* @public
	* @returns ForwardConfig
	*/
	function findForwardConfig($name) {

		if($this->forwards == NULL) {
			return NULL;
		}

		return $this->forwards->get($name);	// ForwardConfig

	}


	/**
	* Return the form bean configurations (ForwardConfig[]) for this 
	* application.  If there are none, a zero-length array is returned.
	*
	* @public
	* @returns array
	*/
	function &findForwardConfigs() {

		if($this->forwards == NULL) {
			$ret = NULL; // Php5 (#63): Only variable references should be returned by reference 
			return $ret;
		}

		$forwards = array();
		$forwards =& $this->forwards;
		return $forwards;

    }


	/**
	* Return the message resources configuration for the specified key,
	* if any; otherwise return <code>null</code>.
	*
	* @param string The key of the data source configuration to return
	* @public
	* @returns MessageResourcesConfig
	*/
	function findMessageResourcesConfig($key) {

		if($this->messageResources == NULL) {
			return NULL;
		}

		return $this->messageResources->get($key);	// MessageResourcesConfig

	}


	/**
	* Return the message resources configurations (MessageResourcesConfig[]) 
	* for this application. If there are none, a zero-length array is returned.
	*
	* @public
	* @returns array
	*/
	function &findMessageResourcesConfigs() {

		if($this->messageResources == NULL) {
			$ret = NULL; // Php5 (#63): Only variable references should be returned by reference 
			return $ret;
		}

		$messageResources = array();
		$messageResources =& $this->messageResources;
		return $messageResources;

    }


	/**
	* Return the configured plug in modules (PlugIn[]) for this application.
	* If there are none, a zero-length array is returned.
	*
	* @public
	* @return array
	*/
	function findPlugIns() {

		if($this->plugIns == NULL) {
			return NULL;
		}

		return $this->plugIns;	// Array

    }


	/**
	* Freeze the configuration of this application.  After this method
	* returns, any attempt to modify the configuration will return
	* an IllegalStateException.
	*
	* @public
	* @returns void
	*/
	function freeze() {

		$this->configured = True;

		// ActionConfigs
		$aconfigs = array();
		$aconfigs =& $this->findActionConfigs();	// ActionConfig[]
		if($aconfigs != NULL) {
			foreach($aconfigs as $key => $val) {
				$aconfigs[$key]->freeze();
			}
		}

		// ControllerConfig
		$controllerConfig =& $this->getControllerConfig();
		if($controllerConfig != NULL) {
			$controllerConfig->freeze();
		}

		// DataSourceConfigs
		$dsconfigs = array();
		$dsconfigs =& $this->findDataSourceConfigs();	// DataSourceConfig[]
		if($dsconfigs != NULL) {
			foreach($dsconfigs as $key => $val) {
				$dsconfigs[$key]->freeze();
			}
		}

		// ExceptionConfigs [not implemented]
		$econfigs = array();
		$econfigs = $this->findExceptionConfigs();	// ExceptionConfig[]
		if($econfigs != NULL) {
			foreach($econfigs as $econfig) {
				$econfig->freeze();
			}
		}

		// FormBeanConfigs
		$fbconfigs = array();
		$fbconfigs =& $this->findFormBeanConfigs();
		if($fbconfigs != NULL) {
			foreach($fbconfigs as $key => $val) {
				$fbconfigs[$key]->freeze();
			}
		}

		// Global ForwardConfigs for this application. ### To-Do### 
		$fconfigs = array();
		$fconfigs =& $this->findForwardConfigs();
		if($fconfigs != NULL) {
			foreach($fconfigs as $key => $val) {
				$fconfigs[$key]->freeze();
			}
		}

		// MessageResourcesConfigs. ### To-Do### 
		$mrconfigs = array();
		$mrconfigs =& $this->findMessageResourcesConfigs();
		if($mrconfigs != NULL) {
			foreach($mrconfigs as $key => $val) {
				$mrconfigs[$key]->freeze();
			}
		}

		// ViewResourceConfig
		$viewResourceConfig =& $this->getViewResourcesConfig();
		if($viewResourceConfig != NULL) {
			$viewResourceConfig->freeze();
		}

	}


	/**
	* Remove the specified action configuration instance.
	*
	* @param ActionConfig	The ActionConfig instance to be removed
	* @public
	* @returns void
	*/
	function removeActionConfig($config) {

		if($this->getConfigured()) {
			#throw new IllegalStateException("Configuration is frozen");
			return;
		}

		$config->setApplicationConfig(NULL);
		HelperUtils::zapArrayElement($config->getPath(), $this->actionConfigs);

    }


	/**
	* Remove the specified exception configuration instance.
	*
	* @param ExceptionConfig The ExceptionConfig instance to be removed
	* @public
	* @returns void
	*/
	function removeExceptionConfig($config) {

		if ($this->getConfigured()) {
			#throw new IllegalStateException("Configuration is frozen");
			return;
		}

		HelperUtils::zapArrayElement($config->getType(), $this->exceptions);

	}


	/**
	* Remove the specified data source configuration instance.
	*
	* @param DataSourceConfig	The DataSourceConfig instance to be removed
	* @public
	* @returns void
	*/
	function removeDataSourceConfig($config) {

		if($this->getConfigured()) {
			#throw new IllegalStateException("Configuration is frozen");
			return;
		}

		HelperUtils::zapArrayElement($config->getKey(), $this->dataSources);

    }


	/**
	* Remove the specified form bean configuration instance.
	*
	* @param FormBeanConfig	The FormBeanConfig instance to be removed
	* @public
	* @returns void
	*/
	function removeFormBeanConfig($config) {

		if($this->getConfigured()) {
			#throw new IllegalStateException("Configuration is frozen");
			return;
		}

		HelperUtils::zapArrayElement($config->getName(), $this->formBeans);

	}


	/**
	* Remove the specified forward configuration instance.
	*
	* @param ForwardConfig	The ForwardConfig instance to be removed
	* @public
	* @returns void
	*/
	function removeForwardConfig($config) {

		if($this->getConfigured()) {
			#throw new IllegalStateException("Configuration is frozen");
			return;
		}

		HelperUtils::zapArrayElement($config->getName(), $this->forwards);

	}


	/**
	* Remove the specified message resources configuration instance.
	*
	* @param MessageResourcesConfig	The MessageResourcesConfig 
	* instance to be removed
	* @public
	* @returns void
	*/
	function removeMessageResourcesConfig($config) {

		if ($this->getConfigured()) {
			#throw new IllegalStateException("Configuration is frozen");
			return;
		}

		HelperUtils::zapArrayElement($config->getKey(), $this->messageResources);

	}


	// ----- Class Serialisation ID ----------------------------------------- //

	/**
	* @public
	* @returns string
	*/
	function _getClassID() {

		// Class ID serialize version info
		$className = 'ApplicationConfig';
		$fileName  = 'ApplicationConfig.php';
		$versionID = '20040811-1100'; // date stamp

		return "$className:$fileName:$versionID";

	}

	/**
	* Instanciated class info
	* @public
	* @returns string
	*/
	function getClassID() {

		// Class ID serialize version info
		return $this->classID;

	}

}
?>