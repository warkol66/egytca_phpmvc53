<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/classes/phpmvc/utils/PhpBeanUtils.php,v 1.6 2006/02/22 08:27:24 who Exp $
* $Revision: 1.6 $
* $Date: 2006/02/22 08:27:24 $
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
* Utility methods for populating php.MVC Object properties
* 
* @author John C. Wildenauer (php.MVC port)<br>
*  Cedits:<br>
*  Craig R. McClanahan	(Apache Struts BeanUtils class)<br>
*  Ralph Schaer			(Apache Struts BeanUtils class)<br>
*  Chris Audley			(Apache Struts BeanUtils class)<br>
*  Rey François			(Apache Struts BeanUtils class)<br>
*  Gregor Raýman			(Apache Struts BeanUtils class)
* @version $Revision: 1.6 $
* @public
*/
class PhpBeanUtils {

	// ----- Private Variables ---------------------------------------------- //

	/**
	* Dummy collection from the Commons Collections API
	*/
	#private static FastHashMap dummy = new FastHashMap(); // !!!

	/**
	* All logging goes through this logger
	* @private
	* @type Log
	*/
	var $log = NULL;	// LogFactory.getLog(BeanUtils.class)

	// ----- Constructor ---------------------------------------------------- //

	function PhpBeanUtils() {

		$this->log = new PhpMVC_Log();	// logging [on|off]
		$this->log->setLog('isDebugEnabled', False);
		$this->log->setLog('isTraceEnabled', False);
	}


	// ----- Public Classes ------------------------------------------------- //

	/**
	* Clone a bean based on the available property getters and setters,
	* <p>To-Do
	*/
	function cloneBean($object) {


	}


	/**
	* Copy property values from the origin bean to the destination bean
	* <p>To-Do
	*/
	function copyProperties($dest, $orig) {


	}


	/**
	* Copy the specified property value to the specified destination bean,
	* <p>To-Do
	*/
	function copyProperty($object, $name, $value) {


	}


	/**
	* Return the entire set of properties for which the specified bean
	* provides a read method.  This map can be fed back to a call to
	* <code>BeanUtils.populate()</code> to reconsitute the same set of
	* properties, modulo differences for read-only and write-only
	* properties, but only if there are no indexed properties.
	* <p>To-Do
	*
	* @param object Bean whose properties are to be extracted
	*
	*/
	function describe($object) {


	}


	/**
	* Return the value of the specified array property of the specified
	* bean, as a String array.
	* <p>To-Do
	*/
	function getArrayProperty($object, $name) {


	}


	/**
	* Return the value of the specified indexed property of the specified
	* bean, as a String.  The zero-relative index of the
	* <p>To-Do
	*/
	#function getIndexedProperty($object, $name) {
	#
	#
	#}


	/**
	* Return the value of the specified indexed property of the specified
	* <p>To-Do
	*/
	function getIndexedProperty($object, $name, $index=0) {


	}


	/**
	* Return the value of the specified indexed property of the specified
	* bean, as a String.  The String-valued key of the required value
	* <p>To-Do
	*/
	#function getMappedProperty($object, $name) {
	#
	#
	#}


	/**
	* Return the value of the specified mapped property of the specified
	* bean, as a String.  The key is specified as a method parameter and
	* <p>To-Do
	*/
	function getMappedProperty($object, $name, $key=NULL) {


	}


	/**
	* Return the value of the (possibly nested) property of the specified
	* name, for the specified bean, as a String.
	* <p>To-Do
	*/
	function getNestedProperty($object, $name) {


	}


	/**
	* Return the value of the specified property of the specified bean,
	* no matter which property reference format is used, as a String.
	* <p>To-Do
	*/
	function getProperty($object, $name) {

		#return (getNestedProperty(bean, name));

	}


	/**
	* Return the value of the specified simple property of the specified
	* bean, converted to a String.
	* <p>To-Do
	*/
	function getSimpleProperty($object, $name) {
		#Object value = PropertyUtils.getSimpleProperty(bean, name);
		#return (ConvertUtils.convert(value));
	}


	/**
	* <p>Populate the properties of the specified PHP object, based on
	* the specified name/value pairs.
	*
	* @param object	The object whose properties are being populated
	* @param array		The array (Map) keyed by property name, with the
	*						corresponding (String or String[]) value(s) to be set
	* @public
	* @returns void
	*/
	function populate(&$object, $properties) {

		$debug = $this->log->getLog('isDebugEnabled');

		// Do nothing unless both arguments have been specified
		if(($object == NULL) || ($properties == NULL)) {
		   return;
		}

		if($debug) {
		   $this->log->debug( 'PhpBeanUtils->populate(' . 
		   							get_class($object).', '.$properties.')['.__LINE__.']' );
		}

		// Loop through the property name/value pairs to be set
		foreach($properties as $name => $value) {

			// Identify the property name and value(s) to be assigned
			if($name == NULL) {
				continue;
			}

			// Perform the assignment for this property
			$this->setProperty($object, $name, $value);

		}

	}


	/**
	* <p>Set the specified property value, performing type conversions as
	* required to conform to the type of the destination property.</p>
	*
	* <p>If the property is read only then the method returns 
	* without throwing an exception.</p>
	*
	* <p>Returns 1 if a class property is set, otherwise returns 0.</p>
	*
	* @param object	The object on which setting is to be performed
	* @param string	The property name (can be nested/indexed/mapped/combo) !!!
	* @param mixed		The value to be set
	* @public
	* @returns integer
	*/
	function setProperty(&$object, $methodName, $value) {

		$trace = $this->log->getLog('isTraceEnabled');	// See: constructor

		// Check if we have a setter method available on this object
		// Note: get_class_methods(...) returns lower case method names
		$classMethods = get_class_methods($object);	// array

		// Setup setter method name. Eg: autoCommit(...) => setAutoCommit(...)
		$methodName = 'set'.ucfirst($methodName);// autoCommit

		// Note: array_search(..) seems dodgy on PHP v4.2.2 Red Hat v6.2
		#if( array_search(strtolower($methodName), $classMethods) === NULL ) {
		#	return;
		#}

		$res = NULL;
		foreach($classMethods as $val) {
			// Have to lowercase both method names to maintain php4 compatibility
			if(strtolower($methodName) == strtolower($val)) {
				$res = True;
			}
		}

		if( is_null($res) ) {
		  return 0;
		}

		// Trace logging (if enabled)
		$strBuff = '';
		if($trace) {
			$strBuff = '  setProperty(';
			$strBuff .= get_class($object);
			$strBuff .= ', ';
			$strBuff .= $methodName;
			$strBuff .= ', ';
			if(value == NULL) {
				$strBuff .= '<NULL>';
			} else if( is_string($value) ) {
				$strBuff .= $value;
         } else {
				$strBuff .= (string) $value;
         }
			$strBuff .= ")";

			$this->log->trace($strBuff);
		}

		$object->$methodName($value);
		return 1; 

    }

}
?>