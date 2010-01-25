<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/lib/digester/test/RulesBaseTestCase.php,v 1.3 2006/02/22 08:46:13 who Exp $
* $Revision: 1.3 $
* $Date: 2006/02/22 08:46:13 $
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
* <p>Test Case for the RulesBase matching rules.
* Most of this material was original contained in the digester test case
* but was moved into this class so that extensions of the basic matching rules
* behaviour can extend this test case.
* </p>
*
* @author John C. Wildenauer (php port)<br>
*  Credits: Craig R. McClanahan (Jakata Struts original)
* @version $Revision: 1.3 $
*/
class RulesBaseTestCase extends TestCase {

	// ----- Instance Variables --------------------------------------------- //

	/**
	* The digester instance we will be processing.
	*/
	var $digester = NULL;


	// ----- Constructors --------------------------------------------------- //

	/**
	* Construct a new instance of this test case.
	*
	* @param string	Name of the test case
	*/
	function RuleTestCase($name) {
	
		parent::TestCase($name);	// build the base class
	
	}


	// ----- Overall Test Methods ------------------------------------------- //

	/**
	* Set up instance variables required by this test case.
	*
	* @public
	* @returns void
	*/
	function setUp() {
	
		$this->digester = new Digester();
		// (Strute class RulesBase)
		$this->digester->setRulesManager(new RulesManager());
	
	}


	/**
	* Tear down instance variables required by this test case.
	*
	* @public
	* @returns void
	*/
	function tearDown() {

		$this->digester = NULL;

	}


	// ----- Individual Test Methods ------------------------------------------- //

	/**
	* Basic test for rule creation and matching.
	*/
	function test_Rules() {

		// Clear any existing rules
		$digester =& $this->digester;
		$digester->clear();
		
		$rulesMan =& $digester->getRulesManager(); // note the reference

		
		$this->assertEquals(0, count($rulesMan->match(NULL, "a")), 
									"Initial rules list is empty");

		// Add a "set properties" rule for the specified element pattern
		$digester->addSetProperties("a");
		// RulesManager tries to find a matching pattern, and returns a
		// set of rules matching the pattern (if any), or an empty array
		// Eg: array {oRuleA, oRuleB, ...oRuleZ}
		$this->assertEquals(1, count($rulesMan->match(NULL, "a")), 
									"Add a matching rule");

		$digester->addSetProperties("b");
		$this->assertEquals(1, count($rulesMan->match(NULL, "a")), 
									"Add a non-matching rule");

		$digester->addSetProperties("a/b");
		$this->assertEquals(1, count($rulesMan->match(NULL, "a")), 
									"Add a non-matching nested rule");

		$digester->addSetProperties("a/b");
		$this->assertEquals(2, count($rulesMan->match(NULL, "a/b")), 
									"Add a second matching rule");

		// clean up

		$rulesMan->clear();

    }


	/**
	* <p>Test matching rules in {@link RulesManager [RulesBase]}.</p>
	*
	* <p>Tests:</p>
	* <ul>
	* <li>exact match</li>
	* <li>tail match</li>
	* <li>longest pattern rule</li>
	* </ul>
	*/
	function test_RulesBase() {

		// Clear any existing rules
		$digester =& $this->digester;
		$digester->clear();

		$rulesMan =& $digester->getRulesManager();

		$this->assertEquals(0, count($rulesMan->rules()), 
						"Initial rules list is empty");

		// We're going to set up
		$digester->addRule("a/b/c/d"	, new TestRule("a/b/c/d"));
		$digester->addRule("*/d"		, new TestRule("*/d"));
		$digester->addRule("*/c/d"		, new TestRule("*/c/d"));

		// Test exact match
		$this->assertEquals(1, count($rulesMan->match(NULL, "a/b/c/d")), 
										"Exact match takes precedence 1");

		$rulesList = $rulesMan->match(NULL, "a/b/c/d");
		$this->assertEquals("a/b/c/d", $rulesList[0]->getIdentifier(), 
										"Exact match takes precedence 2");

		// Test wildcard tail matching ("a/b/d" => "*/d")
		$this->assertEquals(1, count($rulesMan->match(NULL, "a/b/d")), 
										"Wildcard tail matching rule 1");

		$rulesList = $rulesMan->match(NULL, "a/b/d");
		$this->assertEquals("*/d", $rulesList[0]->getIdentifier(), 
										"Wildcard tail matching rule 2");

		// Test the longest matching pattern rule ("x/c/d")
		$this->assertEquals(1, count($rulesMan->match(NULL, "x/c/d")), 
										"Longest tail rule 1");

		$rulesList = $rulesMan->match(NULL, "x/c/d");
		$this->assertEquals("*/c/d", $rulesList[0]->getIdentifier(), 
										"Longest tail rule 2");

		// Test wildcard tail matching at the top level,
		// i.e. the wildcard is nothing
		$digester->addRule("*/a", new TestRule("*/a"));
		$this->assertEquals(1, count($rulesMan->match(NULL,"a")), 
										"Wildcard tail matching rule 3");
	
		$this->assertEquals(0, count($rulesMan->match(NULL,"aa")), 
										"Wildcard tail matching rule 3 (match too much)");

		// Clean up
		$digester->clear();

	}


	/**
	* Test basic matchings involving namespaces.
	*/
	function Xtest_BasicNamespaceMatching() {

		// Later date. NamespaceAware not yet implemented

    }


	/**
	* Rules must always be returned in the correct order.
	*/
	function test_Ordering() {

		// Clear any existing rules
		$digester =& $this->digester;
		$digester->clear();

		$rulesMan =& $digester->getRulesManager();

		$this->assertEquals(0, count($rulesMan->rules()), 
										"Initial rules list is empty");

		// Set up rules 
		// Pushes new Rule object onto the **end** of the rules stack
		$digester->addRule("alpha/beta/gamma", new TestRule("one"));
		$digester->addRule("alpha/beta/gamma", new TestRule("two"));
		$digester->addRule("alpha/beta/gamma", new TestRule("three"));

		
		// Test that rules are returned in set order

		// RulesManager returns all rules matching the pattern "alpha/beta/gamma"
		// If more than one Rule instance matches, they must be returned in 
		// the order originally registered through the RulesManager->add() method.
		
		$rulesList = $rulesMan->match(NULL, "alpha/beta/gamma");
		
		// We should have three rules
		$this->assertEquals(3, count($rulesList), 
										"Testing ordering mismatch (A)");
		
		// First rule (first in - first out)
		$this->assertEquals("one"	, $rulesList[0]->getIdentifier(), 
										"Testing ordering mismatch (B)");
		// Second rule (second in - second out)						
		$this->assertEquals("two"	, $rulesList[1]->getIdentifier(), 
										"Testing ordering mismatch (C)");
		// Last rule (last in - last out)
		$this->assertEquals("three", $rulesList[2]->getIdentifier(), 
										"Testing ordering mismatch (D)");
		
		// Clean up
		$digester->clear();

    }
}
?>