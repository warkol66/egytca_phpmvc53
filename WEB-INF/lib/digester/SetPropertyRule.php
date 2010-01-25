<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/lib/digester/SetPropertyRule.php,v 1.4 2006/02/22 08:49:31 who Exp $
* $Revision: 1.4 $
* $Date: 2006/02/22 08:49:31 $
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
* Rule implementation that sets an individual property on the object at the
* top of the stack, based on attributes with specified names.
*
* @author John C.Wildenauer (php.MVC port)<br>
*  Credits:<br>
*  Craig McClanahan (original Jakarta Struts: see jakarta.apache.org)
* @version $Revision: 1.4 $
*/
class SetPropertyRule extends Rule {

	// ----- Instance Variables --------------------------------------------- //

	/**
	* The attribute that will contain the property name.
	* @type string
	*/
	var $name = NULL;	// protected String

	/**
	* The attribute that will contain the property value.
	*	Eg: <server><set-property property="host" value="darkstar1"/> ... </server>
	* @type string
	*/
	var $value = NULL;	// protected String


	// ----- Constructors --------------------------------------------------- //

	/**
	* Construct a "set property" rule with the specified name and value
	* attributes.
	*
	* <p>Example 1: attribute key/value pairs<br>
	* <code>
	* &lt;server/settings host="darkstar1" port="2010"/&gt; ... &lt;/server&gt; <br>
	* $digester->addRule("server/settings", new SetPropertyRule('host')); <br>
	* $digester->addRule("server/settings", new SetPropertyRule('port'));
	* </code>
	*
	* <p>Example 2: named attribute key/value pairs<br>
	* <code>
	* &lt;server/settings&gt;&lt;set-property property="host" value="darkstar1"/&gt; ... &lt;/server&gt;
	* $digester->addRule("root/star/set-property",  new SetPropertyRule('property', 'value')); <br>
	* </code><br>
	*
	* <p>Note: If the <code>$value</code> class attibute is not set (as in example 1
	* above) we assume this is a standard property setter, as per example 1.<br>
	* Otherwise the property setter must be as per example 2<br>
	* (set-property property="host" value="darkstar1").
	*
	* @param string The name of the attribute that will contain the name of the
	*  property to be set.
	* @param string The name of the attribute that will contain the value to which
	*  the property should be set.
	*/
	function SetPropertyRule($name='', $value='') {

		$this->name		= $name;
		$this->value	= $value;

	}


	// ----- Public Methods ------------------------------------------------- //

	/**
	* Process the beginning of this element.
	*
	* @param array The attribute list of this element
	* @param Digester A reference to the digester instance 
	* @public
	* @returns void
	*/
	function begin($attributes, &$digester) {

		$this->digester =& $digester;
		$debug = $this->digester->log->getLog('isDebugEnabled'); 

		// Identify the actual property name and value to be used
		$actualName		= NULL;	// String
		$actualValue	= NULL;	// String
	
		if($this->value == NULL) {
			// attribute key/value pairs
			if( array_key_exists($this->name, $attributes) ) {
				$actualName = $this->name;
				$actualValue = $attributes[$this->name];
			}
		} else {
			// named attribute key/value pairs
			foreach($attributes as $attribName => $attribValue) {
				// Look for our property "name"/"value" in the attributes list
				if($attribName == $this->name) {
					$actualName = $attribValue;	// found the property name
				} elseif($attribName == $this->value) {
				    $actualValue = $attribValue;	// found the property value
				}
			}
		}

		$values[$actualName] = $actualValue;// {$actualName=>$actualValue}

		// Populate the corresponding property of the top object
		$oTop =& $this->digester->peek();
		
		$debug = $this->digester->log->getLog('isDebugEnabled');
		if($debug) {
			$log = $this->digester->log;
			$log->debug('[SetPropertyRule]{'.$this->digester->match.'} Set '.
			get_class($oTop).' property '.$actualName .' to '.$actualValue);
		}

		# Fix-this: populate the bean properties directly
		#$oTop->populate($values); !!!!!!!!!!!!!!!
		$oTop->addProperty($actualName, $actualValue);

	}


	/**
	* Render a printable version of this Rule.
	*
	* @public
	* @returns string
	*/
	function toString() {

		$sb  = 'SetPropertyRule[';	// StringBuffer
		$sb .= 'name=';
		$sb .= $this->name;
		$sb .= ', value=';
		$sb .= $this->value;
		$sb .= ']';
		return $sb;
	}

}
?>