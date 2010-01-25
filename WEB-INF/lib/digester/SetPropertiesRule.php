<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/lib/digester/SetPropertiesRule.php,v 1.3 2006/02/22 08:49:10 who Exp $
* $Revision: 1.3 $
* $Date: 2006/02/22 08:49:10 $
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
* Rule implementation that sets properties on the object at the top of the
* stack, based on attributes with corresponding names.
*
* @author John C.Wildenauer (php.MVC port)<br>
*  Credits:<br>
*  Craig McClanahan (original Jakarta Struts: see jakarta.apache.org)
* @version $Revision: 1.3 $
* @public
*/
class SetPropertiesRule extends Rule {

	// ----- Private Instance Variables ------------------------------------- //

	/** 
	* Attribute names used to override natural attribute->property mapping
	* @type array
	*/
	var $attributeNames = array();

	/** 
	* Property names used to override natural attribute->property mapping
	* @type array
	*/    
	var $propertyNames = array();


	// ----- Constructors --------------------------------------------------- //

	/** 
	* <p>Constructor allows attribute->property mapping to be overriden.</p>
	*
	* <p>Two arrays are passed in. 
	* One contains the attribute names and the other the property names.
	* The attribute name / property name pairs are match by position
	* In order words, the first string in the attribute name list matches
	* to the first string in the property name list and so on.</p>
	*
	* <p>If a property name is null or the attribute name has no matching
	* property name, then this indicates that the attibute should be ignored.</p>
	* 
	* <h5>Example One</h5>
	* <p> The following constructs a rule that maps the <code>alt-city</code>
	* attribute to the <code>city</code> property and the <code>alt-state</code>
	* to the <code>state</code> property. 
	* All other attributes are mapped as usual using exact name matching.
	* <code><pre>
	*      SetPropertiesRule(
	*                array("alt-city" , "alt-state"), 
	*                array("city"     , "state") );
	* </pre></code>
	*
	* <h5>Example Two</h5>
	* <p> The following constructs a rule that maps the <code>class</code>
	* attribute to the <code>className</code> property.
	* The attribute <code>ignore-me</code> is not mapped.
	* All other attributes are mapped as usual using exact name matching.
	* <code><pre>
	*      SetPropertiesRule(
	*                 array("class"     , "ignore-me"), 
	*                 array("className"              ) );
	* </pre></code>
	*
	* @param array The names of attributes to map
	* @param array The names of properties mapped to
	*/
	function SetPropertiesRule($attributeNames=NULL, $propertyNames=NULL) {

		if(is_array($attributeNames) && is_array($propertyNames) ) {

 			$this->attributeNames = $attributeNames;
 			$this->propertyNames = $propertyNames;

 		}
 
	}


	// ----- Public Methods ------------------------------------------------- //

	/**
	* Process the beginning of this element.
	*
	* @param array The attribute list of this element
	*		{propertyName=>propretyValue, propertyName=>propretyValue,...}
	* @param Digester A reference to the digester instance
	* @public
	* @returns void
	*/
	function begin($attributes, &$digester) {

		$this->digester =& $digester;

		$debug = $this->digester->log->getLog('isDebugEnabled');

		// Identify the actual property names and values to be used
		$name 	= '';
		$values	= array();// array({$actualName=>$actualValue}, ...)

		$propNamesLength = count($this->propertyNames);

		// Loop through the xml element attribute names and corresponding values
		foreach($attributes as $attribName => $attribValue) {

			$name = $attribName;

			// Loop through the attribute->property mapping overrides
			$n = 0;
			foreach($this->attributeNames as $attName) {

				if($attName == $attribName) {
					if($n < $propNamesLength) {
						$name = $this->propertyNames[$n];
					} else {
						$name = NULL;
					}
				}

				$n++;

			}

			$values[$name] = $attribValue;// {$attribName=>$attribValue}

		}


		// Populate the corresponding properties of the top object
		$oTop =& $this->digester->peek();

		if($debug) {
			$log = $this->digester->log;
			$log->debug('[SetPropertiesRule]{'.$this->digester->match.
							'} Set '.get_class($oTop).' properties');
		}


		// Build a set of attribute names and corresponding values
		// Figure how Struts does this !!!!!!!!!!
		$attribName = $attribValue = NULL;
		foreach($values as $attribName => $attribValue) {

			// No matching property name given, so we skip the xml configuration
			if($attribName == NULL) {
				continue;
			}

			if($debug) {
				$log = $this->digester->log;
				$log->debug("[SetPropertiesRule]{".$this->digester->match.
				"} Setting property '".$attribName."' => '".$attribValue."'");
			}

			$oTop->addProperty($attribName, $attribValue);

		}

	}


	/**
	* Render a printable version of this Rule.
	*
	* @returns string
	* @public
	*/
	function toString() {

		$sb  = 'SetPropertiesRule[';	// StringBuffer
		$sb .= ']';
		return $sb;

    }
}
?>