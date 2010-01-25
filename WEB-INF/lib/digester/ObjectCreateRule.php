<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/lib/digester/ObjectCreateRule.php,v 1.6 2006/02/22 08:22:05 who Exp $
* $Revision: 1.6 $
* $Date: 2006/02/22 08:22:05 $
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
* Rule implementation that creates a new object and pushes it
* onto the object stack. When the element is complete, the
* object will be popped
*
* @author John C. Wildenauer (php.MVC port)<br>
*  Credits:<br>
*  Craig McClanahan (original Jakarta Struts: see jakarta.apache.org)<br>
*  Scott Sanders (original Jakarta Struts: see jakarta.apache.org)
*  
* @version $Revision: 1.6 $
*/
class ObjectCreateRule extends Rule {

	// ----- Instance Variables --------------------------------------------- //

	/**
	* The attribute containing an override class name if it is present.
	* @type string
	*/
	var $attributeName = NULL;


	/**
	* The class name of the object to be created. Eg: 'DataSourceConfig'
	* @type string
	*/
	var $className = NULL;


	// ----- Constructors --------------------------------------------------- //

	/**
	* Construct an object create rule with the specified class name and an
	* optional attribute name containing an override.
	*
	* @param string Class name of the config object to be created
	*	 Eg: 'DataSourceConfig'
	* @param string phpmvc-config xml attribute name which, if
	*   present, contains an override of the config class name to create. 
	*   Eg: <data-source ... className="MyDataSourceConfig">
	*	 Note: In this case "MyDataSourceConfig" must be a descendant of 
	*	 "DataSourceConfig". This parameter is optional.
	* @returns void
	*/
	function ObjectCreateRule($className, $attributeName=NULL) {

		$this->className		= $className;
		$this->attributeName	= $attributeName;

	}


	// ----- Public Methods ------------------------------------------------- //

	/**
	* Process the beginning of this element.
	*
	* @param array  The attribute list of this xml element
	* @param Digester A reference to the digester instance
	* @public
	* @returns void
	*/
	function begin($attributes=NULL, &$digester) {

		$this->digester =& $digester;

		// Identify the name of the class to instantiate
		$value			= NULL;
		$realClassName	= NULL;
		$realClassName = $this->className;	// String

		// Setup an alternative class name from the Attributes list
		// attributeName [optional] containing an override class name
		if($this->attributeName != NULL) {
			// Look up an attribute's value by index (Java Attributes interface)
			if( array_key_exists($this->attributeName, $attributes) ) {
				$value = $attributes[$this->attributeName];
			}

			if($value != NULL) {
             $realClassName = $value;
			}
		}

		$debug = $this->digester->log->getLog('isDebugEnabled');
		if($debug) {
			$log = &$this->digester->log;	// the Loger
			$log->debug("ObjectCreateRule->Begin(){" . $this->digester->match .
                    "}New " . $realClassName);
		}

		// Instantiate the new object and push it on the context stack
		#### TO DO ####
		#$classLoader = $this->digester.getClassLoader();
		$oRule = NULL;
		#$oRule = $classLoader($realClassName);
		$oRule = NULL;

		// Check if the class had already been defined. Tripped up the PlugIns !!!
		if(! class_exists( $realClassName ) ){
			include_once $realClassName.'.php';
		}

		$oRule =& new $realClassName;
		$this->digester->push($oRule);
		#### TO DO ####
    }


	/**
	* Process the end of this element.
	*
	* @param Digester A reference to the digester instance
	* @public
	* @returns void
	*/
	function end(&$digester) {

		$this->digester = &$digester;

		$top = NULL;	// Object at top-of-stack. Eg: dataSourceConfig
		$top = $this->digester->pop();

		$debug = $this->digester->log->getLog('isDebugEnabled');
		if($debug) {
			$log = &$this->digester->log;	// the Loger
			$log->debug( "ObjectCreateRule->end(){" . $this->digester->match .
                    "} Pop " . get_class($top) );
		}
	}


	/**
	* Render a printable version of this Rule.
	*
	* @public
	* @returns string
	*/
	function toString() {

		$sb = NULL;
		$sb .= 'ObjectCreateRule[';
		$sb .= 'className=';
		$sb .= $this->className;
		$sb .= ', attributeName=';
		$sb .= $this->attributeName;
		$sb .= ']';

		return $sb;

	}
}
?>