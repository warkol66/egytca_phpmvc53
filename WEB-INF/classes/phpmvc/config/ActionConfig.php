<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/classes/phpmvc/config/ActionConfig.php,v 1.8 2006/02/22 06:40:13 who Exp $
* $Revision: 1.8 $
* $Date: 2006/02/22 06:40:13 $
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
* <p>A PHP Bean representing the configuration information of an
* <code>&lt;action&gt;</code> element from a php.MVC application
* configuration file.</p>
*
* @author John C Wildenauer (php.MVC port)<br>
*  Credits:<br>
*  Craig R. McClanahan (Tomcat/catalina class: see jakarta.apache.org)
* @version $Revision: 1.8 $
* @public
*/
class ActionConfig {

	// ----- Protected Instance Variables ----------------------------------- //

	/**
	* Has configuration of this component been completed?
	* @private
	* @type boolean
	*/
	var $configured = False;

	/**
	* The set of exception handling configurations for this
	* action, if any, keyed by the <code>type</code> property.
	*
	* @private
	* @type array
	*/
	var $exceptions = array();	// new HashMap()

	/**
	* The set of local forward configurations for this action, if any,
	* keyed by the <code>name</code> property.
	*
	* @private
	* @type array
	*/
	var $forwards = array();	// new HashMap()


	// ----- Properties ----------------------------------------------------- //

	/**
	* The application configuration with which we are associated.
	* @private
	* @type ApplicationConfig
	*/
	var $applicationConfig = NULL;

	/**
	* The request-scope or session-scope attribute name under which our
	* form bean is accessed, if it is different from the form bean's
	* specified <code>name</code>.
	*
	* @private
	* @type string
	*/
	var $attribute = NULL;

	/**
	* Context-relative path of the web application resource that will process
	* this request via RequestDispatcher.forward(), instead of instantiating
	* and calling the <code>Action</code> class specified by "type".
	* Exactly one of <code>forward</code>, <code>include</code>, or
	* <code>type</code> must be specified.
	*
	* @private
	* @type string
	*/
	var $forward = NULL;

	/**
	* Context-relative path of the web application resource that will process
	* this request via RequestDispatcher.include(), instead of instantiating
	* and calling the <code>Action</code> class specified by "type".
	* Exactly one of <code>forward</code>, <code>include</code>, or
	* <code>type</code> must be specified.
	*
	* @private
	* @type string
	*/
	var $include = NULL;

	/**
	* Context-relative path of the input form to which control should be
	* returned if a validation error is encountered.  Required if "name"
	* is specified and the input bean returns validation errors.
	*
	* @private
	* @type string
	*/
	var $input = NULL;

	/**
	* Fully qualified Java class name of the
	* <code>MultipartRequestHandler</code> implementation class used to
	* process multi-part request data for this Action.
	*
	* @privare
	* @type string
	*/
	var $multipartClass = NULL;

	/**
	* Name of the form bean, if any, associated with this Action.
	*
	* @private
	* @type string
	*/
	var $name = NULL;

	/**
	* General purpose configuration parameter that can be used to pass
	* extra iunformation to the Action instance selected by this Action.
	* Can be used uith the ActionForward class to forward to the
	* context-relative URI specified by the <action ... parameter="myPage.php">
	* for this ActionMapping.
	*
	* @private
	* @returns string
	*/
	var $parameter = NULL;

	/**
	* Context-relative path of the submitted request, starting with a
	* slash ("/") character, and omitting any filename extension if
	* extension mapping is being used.
	*
	* @private
	* @type string
	*/
	var $path = NULL;

	/**
	* Prefix used to match request parameter names to form ben property
	* names, if any.
	*
	* @private
	* @type string
	*/
	var $prefix = NULL;

	/**
	* Comma-delimited list of security role names allowed to request
	* this Action.
	*
	* @private
	* @type string
	*/
	var $roles = NULL;

	/**
	* The set of security role names used to authorize access to this
	* Action, as an array for faster access.
	*
	* @private
	* @type string
	*/
	var $roleNames = '';

	/**
	* Identifier of the scope ("request" or "session") within which
	* our form bean is accessed, if any.
	*
	* @private
	* @type string
	*/
	var $scope = "session";

	/**
	* Suffix used to match request parameter names to form bean property
	* names, if any.
	*
	* @private
	* @type string
	*/
	var $suffix = NULL;

	/**
	* Fully qualified Java class name of the <code>Action</code> class
	* to be used to process requests for this mapping if the
	* <code>forward</code> and <code>include</code> properties are not set.
	* Exactly one of <code>forward</code>, <code>include</code>, or
	* <code>type</code> must be specified.
	*
	* @private
	* @type string
	*/
	var $type = NULL;

	/**
	* Should this Action be configured as the default one for this
	* application?
	*
	* @private
	* @type boolean
	*/
	var $unknown = False;

	/**
	* Should the <code>validate()</code> method of the form bean associated
	* with this action be called?
	*
	* @private
	* @type string
	*/
	var $validate = True;

	/**
	* @public
	* @returns ApplicationConfig
	*/
	function getApplicationConfig() {
		return $this->applicationConfig;
	}

	/**
	* @param ApplicationConfig
	* @public
	* @returns void
	*/
	function setApplicationConfig(&$applicationConfig) {
		if($this->configured) {
			#throw new IllegalStateException("Configuration is frozen");
			return 'Configuration is frozen';
		}

		$this->applicationConfig =& $applicationConfig;
	}


	/**
	* @public
	* @returns string
	*/
	function getAttribute() {
        if($this->attribute == NULL) {
            return $this->name; // form bean name !!!!!!!!!
        } else {
            return $this->attribute;
        }
    }

	/**
	* @param string
	* @public
	* @returns void
	*/
	function setAttribute($attribute) {
		if($this->configured) {
			#throw new IllegalStateException("Configuration is frozen");
			return 'Configuration is frozen';
		}
		$this->attribute = $attribute;
	}


	/**
	* @public
	* @returns string
	*/
	function getForward() {
		return $this->forward;
	}

	/**
	* @param string
	* @public
	* @returns void
	*/
	function setForward($forward) {
		if($this->configured) {
			#throw new IllegalStateException("Configuration is frozen");
			return 'Configuration is frozen';
		}

		$this->forward = $forward;
	}


	/**
	* @public
	* @returns string
	*/
	function getInclude() {
		return $this->include;
	}

	/**
	* @param string
	* @public
	* @returns void
	*/
	function setInclude($include) {
		if($this->configured) {
			#throw new IllegalStateException("Configuration is frozen");
         return 'Configuration is frozen';
		}

		$this->include = $include;
	}


	/**
	* @public
	* @returns string
	*/
	function getInput() {
		return $this->input;
	}

	/**
	* @param string
	* @public
	* @returns void
	*/
	function setInput($input) {
		if($this->configured) {
			#throw new IllegalStateException("Configuration is frozen");
			return 'Configuration is frozen';
		}
		$this->input = $input;
    }


	/**
	* @public
	* @returns string
	*/
	function getMultipartClass() {
		return $this->multipartClass;
	}

	/**
	* @param string
	* @public
	* @returns void
	*/
	function setMultipartClass($multipartClass) {
		if($this->configured) {
			#throw new IllegalStateException("Configuration is frozen");
			return 'Configuration is frozen';
		}
		$this->multipartClass = $multipartClass;
	}


	/**
	* @public
	* @returns string
	*/
	function getName() {
		return $this->name;
	}

	/**
	* @param string
	* @public
	* @returns void
	*/
	function setName($name) {
		if($this->configured) {
			#throw new IllegalStateException("Configuration is frozen");
			return 'Configuration is frozen';
		}
		$this->name = $name;
	}


	/**
	* @public
	* @returns string
	*/
	function getParameter() {
		return $this->parameter;
	}

	/**
	* @param string
	* @public
	* @returns void
	*/
	function setParameter($parameter) {
		if($this->configured) {
			#throw new IllegalStateException("Configuration is frozen");
			return 'Configuration is frozen';
		}
		$this->parameter = $parameter;
	}


	/**
	* @public
	* @returns string
	*/
	function getPath() {
        return $this->path;
    }

	/**
	* @param string
	* @public
	* @returns void
	*/
	function setPath($path) {
		if ($this->configured) {
			#throw new IllegalStateException("Configuration is frozen");
			return 'Configuration is frozen';
		}
		$this->path = $path;
	}


	/**
	* @public
	* @returns string
	*/
	function getPrefix() {
		return $this->prefix;
	}

	/**
	* @param string
	* @public
	* @returns void
	*/
	function setPrefix($prefix) {
		if($this->configured) {
			#throw new IllegalStateException("Configuration is frozen");
			return 'Configuration is frozen';
		}
		$this->prefix = $prefix;
	}


	/**
	* @public
	* @returns string
	*/
	function getRoles() {
		return $this->roles;
	}

	/**
	* @param string
	* @public
	* @returns void
	*/
	function setRoles($roles) {
		if($this->configured) {
			#throw new IllegalStateException("Configuration is frozen");
			return 'Configuration is frozen';
		}
		$this->roles = $roles;
		if($this->roles == NULL) {
			$this->roleNames = '';
			return;
		}

		$list = array();
		while(True) {
			$comma = strpos($roles, ',');
			if($comma === False) {
				break;
			}
			$list[] = trim( substr($roles, 0, $comma) );
			$roles = substr($roles, $comma + 1);
		}

		// Add the final role item
		$roles = trim($roles);
		if(strlen($roles) > 0) {
			$list[] = $roles;
		}

		$this->roleNames = $list;

	}


	/**
	* @public
	* @returns string
	*/
	function getRoleNames() {
		return $this->roleNames;
	}


	/**
	* @public
	* @returns string
	*/
	function getScope() {
        return $this->scope;
    }

	/**
	* @param string
	* @public
	* @returns void
	*/
	function setScope($scope) {
		if($this->configured) {
			#throw new IllegalStateException("Configuration is frozen");
			return 'Configuration is frozen';
		}
		$this->scope = $scope;
	}


	/**
	* @public
	* @returns string
	*/
	function getSuffix() {
		return $this->suffix;
	}

	/**
	* @param string
	* @public
	* @returns void
	*/
	function setSuffix($suffix) {
		if($this->configured) {
			#throw new IllegalStateException("Configuration is frozen");
			return 'Configuration is frozen';
		}
		$this->suffix = $suffix;
	}


	/**
	* @public
	* @returns string
	*/
	function getType() {
		return $this->type;
	}

	/**
	* @param string
	* @public
	* @returns void
	*/
	function setType($type) {
		if($this->configured) {
			#throw new IllegalStateException("Configuration is frozen");
			return 'Configuration is frozen';
		}
		$this->type = $type;
	}


	/**
	* @public
	* @returns boolean
	*/
	function getUnknown() {
		return $this->unknown;
	}

	/**
	* @param boolean
	* @public
	* @returns void
	*/
	function setUnknown($unknown) {
		if($this->configured) {
			#throw new IllegalStateException("Configuration is frozen");
			return 'Configuration is frozen';
		}
		$this->unknown = $unknown;
	}


	/**
	* @public
	* @returns string
	*/
	function getValidate() {
		return $this->validate;
	}

	/**
	* @param string
	* @public
	* @returns void
	*/
	function setValidate($validate) {
		if($this->configured) {
			#throw new IllegalStateException("Configuration is frozen");
			return 'Configuration is frozen';
		}
		$this->validate = $validate;
	}


	// ----- Public Methods ------------------------------------------------- //

	/** 
	* 
	* Add a new custom configuration property.
	*
	* @param string	Custom property name
	* @param string	Custom property value
	* @public
	* @returns void
	*/
	function addProperty($name, $value) {

		if($this->configured) {
			return 'Configuration is frozen';
		}

		// XML boolean needs to be converted
		if( strtolower($value) == 'true') {
			$value = True;
		} elseif(strtolower($value) == 'false') {
			$value = False;
		} 

		// Set the method matching the "name" parameter
		$beanUtils = new PhpBeanUtils();
		$beanUtils->setProperty($this, $name, $value);

	}


	/**
	* Add a new <code>ExceptionConfig</code> instance to the set associated
	* with this action.
	*
	* @param ExceptionConfig	The new configuration instance to be added
	* @public
	* @returns void
	*/
	function addExceptionConfig($config) {

		if($this->configured) {
			#throw new IllegalStateException("Configuration is frozen");
			return 'Configuration is frozen';
		}

		$this->exceptions[$config->getType()] = $config;

	}


	/**
	* Add a new <code>ForwardConfig</code> instance to the set of global
	* forwards associated with this action.
	*
	* @param ForwardConfig	The new configuration instance to be added
	* @public
	* @returns void
	*/
	function addForwardConfig($config) {

		if($this->configured) {
			#throw new IllegalStateException("Configuration is frozen");
			return 'Configuration is frozen';
		}
		$key = $config->getName();
		$this->forwards[$key] = $config;

	}


	/**
	* Return the exception configuration for the specified type, if any;
	* otherwise return <code>NULL</code>.
	*
	* @param string	Exception class name to find a configuration for
	* @public
	* @returns ExceptionConfig
	*/
	function findExceptionConfig($type) {

		return $this->exceptions[$type]; // (ExceptionConfig)

	}


	/**
	* Return the exception configurations for this action.  If there
	* are none, a zero-length array is returned.
	*
	* @public
	* @returns ExceptionConfig
	*/
	function findExceptionConfigs() {

		#ExceptionConfig results[] = new ExceptionConfig[exceptions.size()];
		#return ((ExceptionConfig[]) $this->exceptions.values().toArray(results));

	}


	/**
	* Return the forward configuration for the specified key, if any;
	* otherwise return <code>NULL</code>.
	*
	* @param string	Name of the forward configuration to return
	* @public
	* @returns ForwardConfig
	*/
	function findForwardConfig($name) {

		return $this->forwards[$name];	// (ForwardConfig)

	}


	/**
	* Return the form bean configurations (ForwardConfig[]) for this 
	* application. If there are none, a zero-length array is returned.
	* 
	* @public
	* @returns ForwardConfig[]
	*/
	function &findForwardConfigs() {

		$forwards = array();
		$forwards =& $this->forwards;
		return $forwards;				// array of ForwardConfig objects or array()

	}


	/**
	* Freeze the configuration of this action.
	* @public
	* @returns void
	*/
	function freeze() {

		$this->configured = True;

		$econfigs = $this->findExceptionConfigs(); // ExceptionConfig[]
		if($econfigs != NULL) {
			foreach($econfigs as $econfig) {
				$econfig->freeze();
			}
		}

		$fconfigs =& $this->findForwardConfigs(); // ForwardConfig[]
		if($fconfigs != NULL) {
			foreach($fconfigs as $key => $val) {
				$fconfigs[$key]->freeze();
			}
		}
	}


	/**
	* Remove the specified exception configuration instance.
	*
	* @param ExceptionConfig	ExceptionConfig instance to be removed
	* @public
	* @returns void
	*/
	function removeExceptionConfig($config) {

		if($this->configured) {
			#throw new IllegalStateException("Configuration is frozen");
			return 'Configuration is frozen';
		}
		#$this->exceptions.remove(config.getType());
		HelperUtils::zapArrayElement($config->getType(), $this->exceptions);

	}


	/**
	* Remove the specified forward configuration instance.
	*
	* @param ForwardConfig	ForwardConfig instance to be removed
	* @public
	* @returns void
	*/
	function removeForwardConfig($config) {

		if($this->configured) {
			#throw new IllegalStateException("Configuration is frozen");
			return 'Configuration is frozen';
		}

		#$this->forwards.remove(config.getName());
		HelperUtils::zapArrayElement($config->getName(), $this->forwards);

	}


	/**
	* Return a String representation of this object.
	*
	* @public
	* @returns string
	*/
	function toString() {

		$strBuff = 'ActionConfig[';
		$strBuff .= 'path=';
		$strBuff .= $this->path;
		if($this->attribute != NULL) {
			$strBuff .= ',attribute=';
			$strBuff .= $this->attribute;
		}
		if($this->forward != NULL) {
			$strBuff .= ',forward=';
			$strBuff .= $this->forward;
		}
		if($this->include != NULL) {
			$strBuff .= ',include=';
			$strBuff .= $this->include;
		}
		if($this->input != NULL) {
			$strBuff .= ',input=';
			$strBuff .= $this->input;
		}
		if($this->multipartClass != NULL) {
			$strBuff .= ',multipartClass=';
			$strBuff .= $this->multipartClass;
		}
		if($this->name != NULL) {
			$strBuff .= ',name=';
			$strBuff .= $this->name;
		}
		if($this->parameter != NULL) {
			$strBuff .= ',parameter=';
			$strBuff .= $this->parameter;
		}
		if($this->prefix != NULL) {
			$strBuff .= ',prefix=';
			$strBuff .= $this->prefix;
		}
		if($this->roles != NULL) {
			$strBuff .= ',roles=';
			$strBuff .= $this->roles;
		}
		if($this->scope != NULL) {
			$strBuff .= ',scope=';
			$strBuff .= $this->scope;
		}
		if($this->suffix != NULL) {
			$strBuff .= ',suffix=';
			$strBuff .= $this->suffix;
		}
		if($this->type != NULL) {
			$strBuff .= ',type=';
			$strBuff .= $this->type;
		}

		return $strBuff;

	}


	// ----- Class Serialisation ID ----------------------------------------- //

	/** JCW
	* Serialize version info. This is to ensure that the serialized
	* php.MVC configuration data stored on disk is compatable with
	* this config class.
	* <p>Update this info if making changes to the config classes that
	* would be incompatable with older versions
	*
	* <p>Returns a serial string, something like:
	*    "$className:$fileName:$versionID"
	*
	* @public
	* @returns string
	*/
	function getClassID() {

		// Class ID serialize version info
		$className = 'ActionConfig';
		$fileName  = 'ActionConfig.php';
		$versionID = '20040528-1130'; // date stamp

		return "$className:$fileName:$versionID";

	}

}
?>