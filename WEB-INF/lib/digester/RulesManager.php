<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/lib/digester/RulesManager.php,v 1.4 2006/02/22 08:47:23 who Exp $
* $Revision: 1.4 $
* $Date: 2006/02/22 08:47:23 $
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
* <p>Default implementation of the <code>Rules</code> interface that supports
* the standard rule matching behavior.  This class can also be used as a
* base class for specialized <code>Rules</code> implementations.</p>
*
* @author Jonn C. Wildenauer<br>
*  Credits:<br>
*  Craig R. McClanahan (original Jakarta Struts)
* @version $Revision: 1.4 $
*/
class RulesManager extends Rules {

	// ----- Instance Variables --------------------------------------------- //

	/**
	* The set of registered Rule instances, keyed by the matching pattern.
	* Each value is a List containing the Rules for that pattern, in the
	* order that they were orginally registered.
	* @type array
	*/
	var $cache = NULL;	// new HashMap()

	/**
	* The Digester instance with which this Rules instance is associated.
	* @type Digester
	*/
	var $digester = NULL;

	/**
	* The namespace URI for which subsequently added <code>Rule</code>
	* objects are relevant, or <code>null</code> for matching independent
	* of namespaces.
	* @type string
	*/
	var $namespaceURI = NULL;	// String

	/**
	* The set of registered Rule instances, in the order that they were
	* originally registered.
	* @type array
	*/
	var $rules = array();	// ArrayList !!


	// ----- Public Properties ---------------------------------------------- //

	/**
	* Constructor
	* 
	*/
	function RulesManager() {

		$this->cache = new HashMap();

	}


	/**
	* Return the Digester instance with which this Rules instance is
	* associated.
	*
	* @public
	* @returns Digester
	*/
	function getDigester() {

		return $this->digester;

	}


	/**
	* Set the Digester instance with which this Rules instance is associated.
	*
	* @param Digester The newly associated Digester instance reference
	* @public
	* @returns void
	*/
	function setDigester(&$digester) {

		$this->digester = &$digester;

	}


	/**
	* Return the namespace URI that will be applied to all subsequently
	* added <code>Rule</code> objects.
	*
	* @public
	* @returns string
	*/
	function getNamespaceURI() {

		return $this->namespaceURI;

	}


	/**
	* Set the namespace URI that will be applied to all subsequently
	* added <code>Rule</code> objects.
	*
	* @param string The namespace URI that must match on all
	*  subsequently added rules, or <code>null</code> for matching
	*  regardless of the current namespace URI
	* @public
	* @returns void
	*/
	function setNamespaceURI($namespaceURI) {

		$this->namespaceURI = $namespaceURI;

	}


	// ----- Public Methods ------------------------------------------------- //

	/**
	* Register a new Rule instance matching the specified pattern.
	* Pushes $rule onto the end of the rules array.
	*
	* @param string The nesting pattern to be matched for this Rule
	* @param Rule The Rule instance to be registered
	* @public
	* @returns void
	*/
	function add($pattern, &$rule) {

		// Get a Rule objects matching this pattern {oRule1, oRule2, oRuleN}
		$list = array();
		$list = $this->cache->getArrayList($pattern);// array of Rule objects
																	// eg: {oRuleA, oRuleB,...}

		if($list == NULL) {
			$list = array();	// 
		} else {
			$list = $list[0];
		}
		
		// Add $oRule obj ref to **end** of $list array
		array_push($list, $rule);

		// [pattern => {[0]=>oRuleA, [1]=>oRuleB,...}]	
		$this->cache->put($pattern, $list);

		if($this->namespaceURI != NULL) {
			$rule->setNamespaceURI($this->namespaceURI);
		}

		// Push $rule onto the end of the rules array 
		array_push($this->rules, $rule);// add oRule object ref to $rules stack	

	}


	/**
	* Clear all existing Rule instance registrations.
	*
	* @public
	* @returns void
	*/
	function clear() {

		$this->cache->clear();	// zap the pattern=>rule_list HashMap
		unset($this->rules);		// zap the rules array

	}


	/**
	* Return a List of all registered Rule instances that match the specified
	* nesting pattern, or a zero-length List if there are no matches.  If more
	* than one Rule instance matches, they <strong>must</strong> be returned
	* in the order originally registered through the <code>add()</code>
	* method.
	*
	* <p>Returns a set of rules matching the pattern (if any), 
	*  or an empty array. Eg: array {oRuleA, oRuleB, ...oRuleZ}
	*
	* @param string The namespace URI for which to select matching rules,
	*  or <code>null</code> to match regardless of namespace URI
	* @param string The nesting pattern to be matched
	*						Eg: "/xxx/yyy/my/forwards"
	* @returns array
	*/
	function &match($namespaceURI=NULL, $pattern) {

		$rulesSet = NULL;	// array()
		$rulesSet =& $this->lookupRules($namespaceURI, $pattern);// Set{key=>val, ..}

		// If no matching rules found for the given pattern
		if( ($rulesSet == NULL) || count($rulesSet) < 1 ) {

			// Find the longest key, ie more discriminant
			$longKey = '';	// String

			$keys = array();
			// A set view of the patterns (keys) contained in this map
			//  Eg: "/xxx/yyy/my/appl", "*/forwards", "*/my/forwards"
			$keys = $this->cache->getKeySet();	// array() of all patterns

			foreach($keys as $key => $v) {	// Eg: {key = 'form-beans'}

				// Wildcards ("*/forwards", )
				if( substr($key, 0, 2) == "*/" ) { // starts with "*/"

               // If pattern ends with (tail -1)
               //  Eg: "/xxx/yyy/my/forwards" == "/forwards"
					$len = strlen(substr($key, 1));
					if( $pattern == substr($key, 2) || 
						substr($pattern, -$len) == substr($key, 1) ) 
					{
						if( strlen($key) > strlen($longKey) ) {
							$rulesSet = $this->lookupRules($namespaceURI, $key);
							$longKey = $key;	// // Find the longest key
						}
					}
				}

         }
		}

		if($rulesSet == NULL) {
			$rulesSet = array();
		}

		return $rulesSet;	// {oRuleA, oRuleB, ...oRuleZ}

	}


	/**
	* Return a List of all registered Rule instances, or a zero-length List
	* if there are no registered Rule instances.  If more than one Rule
	* instance has been registered, they <strong>must</strong> be returned
	* in the order originally registered through the <code>add()</code>
	* method.
	*
	* @public
	* @returns array
	*/
	function rules() {

		return $this->rules;

	}


	// ----- Protected Methods ---------------------------------------------- //

	/**
	* Return a List of Rule instances for the specified pattern that also
	* match the specified namespace URI (if any).  If there are no such
	* rules, return <code>null</code>.
	*
	* @param string String Namespace URI to match, or <code>null</code> to
	*  select matching rules regardless of namespace URI
	* @param string The pattern to be matched
	* @public
	* @returns array
	*/
	function &lookupRules($namespaceURI=NULL, $pattern) {

		// Optimize when no namespace URI is specified
		$ruleList = NULL;
		// values (oRuleA, ..)
		$ruleList = $this->cache->getArrayList( strtolower($pattern) );

		if($ruleList != NULL) {
			$ruleList = $ruleList[0];
		} else {
			$ret = NULL; // Php5 (#63): Only variable references should be returned by reference 
			return $ret;
		}

		// No namespace specified, so just return a list of
		//   rule objects. {oRulea, oRuleB, ...}
		if( ($namespaceURI == NULL) || (strlen($namespaceURI) == 0) ) {
			return $ruleList;
		}

		// Select only Rules that match on the specified namespace URI
		$resultList = array();
		foreach($ruleList as $ruleItem) {
			// Either namespace matches, or the rule has no namespace !!
			if( ($namespaceURI == $ruleItem->getNamespaceURI() ) ||
					($ruleItem->getNamespaceURI() == NULL) ) {
				array_push($resultList, $ruleItem);
			}
		}

		return $resultList;

	}
}
?>