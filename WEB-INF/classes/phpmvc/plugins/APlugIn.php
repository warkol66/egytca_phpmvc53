<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/classes/phpmvc/plugins/APlugIn.php,v 1.2 2006/02/22 07:02:12 who Exp $
* $Revision: 1.2 $
* $Date: 2006/02/22 07:02:12 $
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
* Abstract APlugIn class
*
* <p>APlugIn is an abstract class that provides a means of dynamically loading
* and accessing additional modules within php.MVC in a pluggable manner. This
* mechanism alleviates the need to hard-code changes into the library.</p>
*
* <p>A PlugIn module wrapper class is used to implement a particular PlugIn,
* and must extend this abstract class. The concrete PlugIn class must implement
* the necessary property setter methods.
* </p>
*
* <p>PlugIn modules are configured in the phpmvc-config.xml file. The PlugIn
* class to load is specified using the plug-in elements "className" attribute
* and the PlugIn identifying key is given by the "key" attribute. Properties
* on the PlugIn module can be set using the &lt;set-property .../&gt; elements.
* </p>
*
* <p>The PlugIn class must implement <code>init()</code> and <code>destroy()
* </code> methods. The ActionServer calls the <code>init()</code> method after
* the PlugIn properties have been set, and can perform additional initialisation
* , if required.</p>
*
* <p>A reference to the PlugIn instance can be retrieved in Action, ActionForm,
* and ActionDispatcher derived classes using the plug-in elements "key" 
* identifier string. For example:<br>
* <code>$myPlugIn =& $this->actionServer->getPlugIn('MY_PLUGIN_KEY_ID');</code>
* </p>
*
* <p>
* An example phpmvc-config.xml configuration file plug-in element using the Smarty
* compiling PHP template engine, is shown below:
* <pre>

  &lt;!-- ========== PlugIns ================================================= --&gt;
  
  &lt;!-- Load our PlugIn class here (case sensetive class name)               --&gt;
  &lt;!-- Note: The attribute names must match the class methods               --&gt;
  &lt;!-- Eg: 'key' maps to 'setKey(..)'                                       --&gt;

*   &lt;plug-in className="SmartyPlugInDriver"
*                  key="SMARTY_PLUGIN"&gt;
*     &lt;!-- And set some custom propertied on the PlugIn class                 --&gt;
*     &lt;!-- Note: The property name must match the class variable name exactly --&gt;
*     &lt;set-property property="caching"       value="1"/&gt;
*     &lt;set-property property="force_compile" value="False"/&gt;
*     &lt;set-property property="template_dir"  value="D:/WWW/SmartyApp/WEB-INF/tpl/"/&gt;
*     &lt;set-property property="compile_dir"   value="D:/WWW/SmartyApp/WEB-INF/smarty_tpl/templates_c/"/&gt;
*     &lt;set-property property="config_dir"    value="D:/WWW/SmartyApp/WEB-INF/smarty_tpl/configs/"/&gt;
*     &lt;set-property property="cache_dir"     value="D:/WWW/SmartyApp/WEB-INF/smarty_tpl/cache/"/&gt;
*   &lt;/plug-in&gt;
* </pre>
* 
* </p>
*
* @author John C. Wildenauer<br>
*  Credits:<br>
*  Craig R. McClanahan (Jakata Struts class) 
* @version $Revision: 1.2 $
*/
class APlugIn {

	// ----- Properties ----------------------------------------------------- //

	/**
	* Has this class instance been initialised yet
	* @type boolean
	*/
	var $init = False;

	/**
	* Class name - set from the xml plug-in element. Eg: "SmartyPlugInDriver"
	* @type string
	*/
	var $className = '';

	/**
	* PlugIn key value - set in the xml plug-in element. Eg: "SMARTY_PLUGIN"
	* @type string
	*/
	var $key = '';

	/**
	* A reference to the PlugIn instance.
	* @type object
	*/
	var $plugIn = NULL;


	/**
	* Set the plugIn class name as set in the xml plug-in element
	* 
	* @private
	* @returns void
	*/
   function setClassName($value) {
      $this->className = $value;
   }

	/**
	* Set the plugIn key value as set in the xml plug-in element
	* 
	* @private
	* @returns void
	*/
   function setKey($value) {
      $this->key = $value;
   }

	/**
	* Return the plugIn key value as set in the xml plug-in element
	* 
	* @public
	* @returns string
	*/
   function getKey() {
      return $this->key;
   }


	/**
	* Add a property.
	*
	* <p>This method sets properties on the PlugIn driver instance and properties
	* on the PlugIn module instance.
	*
	* <p>If plug-in element attributes "className" or "key" are found, they are
	* mapped to methods <code>setClassName()</code> and <code>setKey()</code> on
	* the PlugIn driver.</p>
	*
	* <p>If the plug-in element contains <code>&lt;set-property .../&gt;</code>
	* elements, class variables on the PlugIn module instance are set. For example,
	* the following set-property element<br>
	* <code>&lt;set-property property="force_compile" value="False"/&gt;</code><br>
	* would result in the PlugIn instance variable "force_compile" being set to "True".
	*
	* <p>Override this method in the concrete PlugIn wrapper class to provide
	* application specific behavior as required.</p>
	*
	* @param string	The property name
	* @param string	The property value
	* @public
	* @returns void
	*/
	function addProperty($name, $value) {

		$propertyName = 'set'.ucfirst($name);
		
		// XML boolean needs to be converted
		if( strtolower($value) == 'true') {
			$value = True;
		} elseif(strtolower($value) == 'false') {
			$value = False;
		}


		// Set methods on the PlugIn driver class ($this->className, $this->key)
		// Eg: <plug-in ... key="SMARTY_PLUGIN" .../> maps to $this->setKey("SMARTY_PLUGIN")
		if( method_exists( $this, $propertyName) ) {
			$this->$propertyName($value);
		} else {
			// Set properties in the PlugIn class.
			// Check for a set-property element, and try to set the class variable directly
			// Eg: <set-property property="force_compile" value="true"/>
			$plugInVars = get_class_vars( get_class($this->plugIn) );		

			if( array_key_exists($name, $plugInVars) ) {
				$p =& $this->plugIn;
				$p->$name = $value;
			} else {
				
				$driverName = ucfirst(get_class($this));				
				print 'Error: '.$driverName.'->addProperty(): Method: '.
						$propertyName." not found<br>\n";
			}
		}

	}


	// ----- Constructor ---------------------------------------------------- //

	function APlugIn() {
	
		// Using a PlugIn class


	}


	// ----- Public Methods ------------------------------------------------- //

	/**
	* Receive notification that this application is starting up, and gives the
	* PlugIn driver an opportunity to initialise itself, if required.
	*
	* <p>This method is called from the ActionServer->getPlugIn() method.
	* The init() method returns immediately if the getPlugIn() method has already
	* been called. Eg: PlugIn initialisation is complete.</p>
	*
	* <p>Override this method in the concrete PlugIn wrapper class to provide
	* application specific behavior as required.</p>
	*
	* @param ApplicationConfig		The ApplicationConfig configuration object.
	*  This parameter is optional.
	* @public
	* @returns void
	*/
	function init($config='') {

		// 
		if($this->init) {
			// We have already been initialised
			return;
		}

		// Initialise the PlugIn as required
		; // .....


		// Done
		$this->init = True;

	}


	/**
	* Receive notification that this application is shutting down.
	*
	* <p>Override this method in the concrete PlugIn wrapper class to provide
	* application specific behavior as required.</p>
	*
	* @public
	* @returns void
	*/
	function destroy() {
		// Called from ActionServer->destroyApplications() [TODO - if reqd]
		$this->plugIn = NULL;
		;

	}

}
?>