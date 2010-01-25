<?php
/*
* $Header: /PHPMVC/phpmvc-base/WEB-INF/lib/digester/test/DigesterTestCase.php,v 1.3 2006/02/22 07:24:43 who Exp $
* $Revision: 1.3 $
* $Date: 2006/02/22 07:24:43 $
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
* <p>Test Case for the Digester class.  These tests exercise the individual
* methods of a Digester, but do not attempt to process complete documents.
* </p>
*
* @author John C. Wildenauer (php port)<br>
*  Credits: Craig R. McClanahan (Jakata Struts original)
* @version $Revision: 1.3 $
*/
class DigesterTestCase extends TestCase {

	// ----- Instance Variables --------------------------------------------- //

	/**
	* The digester instance we will be processing.
	*/
	var $digester = NULL;


	/**
	* The set of public identifiers, and corresponding resource names,
	* for the versions of the DTDs that we know about.  There
	* <strong>MUST</strong> be an even number of Strings in this array.
	*/
	#registrations[] = { ... }; // protected static final String


	// ----- Constructors --------------------------------------------------- //

	/**
	* Construct a new instance of this test case.
	*
	@param name String - Name of the test case
	*/
	function DigesterTestCase($name) {
	
		parent::TestCase($name);	// build the base class
	
	}


	// ----- Overall Test Methods ------------------------------------------- //

	/**
	* Set up instance variables required by this test case.
	*
	* @access public
	* @return void
	*/
	function setUp() {
	
		$this->digester = new Digester();
		// (Strute class RulesBase)
		$this->digester->setRulesManager(new RulesManager());
	
	}


	/**
	* Tear down instance variables required by this test case.
	*
	* @access public
	* @return void
	*/
	function tearDown() {

		$this->digester = NULL;

	}


	// ----- Individual Test Methods ------------------------------------------- //

	/**
	* Test the basic property getters and setters.
	*/
	function test_Properties() {

		$digester =& $this->digester;

		# Later date
		#$this->assertEquals(NULL ,	$digester->getErrorHandler(), 
		#					"Initial error handler is null");         
		#$digester->setErrorHandler($digester);
		#$this->assert($digester->getErrorHandler() == $digester, 
		#					"Set error handler is digester");
		#$digester->setErrorHandler(NULL);
		#$this->assertEquals(NULL, $digester->getErrorHandler(),
		#					"Reset error handler is null");
		#
		#$this->assert(!$digester->getNamespaceAware(),
		#					"Initial namespace aware is false");
		#$digester->setNamespaceAware(True);
		#$this->assertTrue($digester->getNamespaceAware(),
		#					"Set namespace aware is true");
		#$digester->setNamespaceAware(False);
		#$this->assert(!$digester->getNamespaceAware(),
		#					"Reset namespace aware is false");

		$this->assert(!$digester->getValidating(),
							"Initial validating is false");
		$digester->setValidating(True);
		$this->assert($digester->getValidating(),
							"Set validating is true");
		$digester->setValidating(False);
		$this->assert(!$digester->getValidating(),
							"Reset validating is false");

    }


	/**
	* Test registration of URLs for specified public identifiers.
	*/
	function Xtest_Registrations() {

		// to do

    }


	/**
	* Basic test for rule creation and matching.
	*
	* Note: Moved to RulesBaseTestCase->test_Rules()
	*/
	function test_Rules() {

		$digester =& $this->digester;
		$rulesManager =& $digester->getRulesManager();


		$this->assertEquals(0, count($rulesManager->match( NULL, 'a')),
									"Initial rules list is empty");
		
		// Add a "set properties" rule for the specified element pattern
		$digester->addSetProperties("a");
		// RulesManager tries to find a matching pattern, and returns a
		// set of rules matching the pattern (if any), or an empty array
		// Eg: array {oRuleA, oRuleB, ...oRuleZ}
		$this->assertEquals(1, count($rulesManager->match( NULL, 'a')),
									"Add a matching rule");

		$digester->addSetProperties("b");
		$this->assertEquals(1, count($rulesManager->match( NULL, 'a')),
									"Add a non-matching rule");

		$digester->addSetProperties("a/b");
		$this->assertEquals(1, count($rulesManager->match( NULL, 'a')),
									"Add a non-matching nested rule");

		$digester->addSetProperties("a/b");
		$this->assertEquals(2, count($rulesManager->match( NULL, 'a/b')),
									"Add a second matching rule");

	}


	/**
	* <p>Test matching rules in {@link Rulesmanager}[RulesBase].</p>
	*
	* Note: Moved to RulesBaseTestCase->test_RulesBase()
	*
	* <p>Tests:</p>
	* <ul>
	* <li>exact match</li>
	* <li>tail match</li>
	* <li>longest pattern rule</li>
	* </ul>
	*/
	function test_RulesBase() {

		$digester =& $this->digester;
		$rulesManager =& $digester->getRulesManager();

		$this->assertEquals(0, count($rulesManager->rules()),
									"Initial rules list is empty");

		// We're going to set up
		$digester->addRule("a/b/c/d", new TestRule("a/b/c/d"));
		$digester->addRule("*/d", new TestRule("*/d"));
		$digester->addRule("*/c/d", new TestRule("*/c/d"));


		// Test exact match (exactly one rule)
		$this->assertEquals(1, count($rulesManager->match(NULL, "a/b/c/d")),
					 "Exact match takes precedence 1");

		$testRule = $rulesManager->match(NULL, "a/b/c/d");	
		$this->assertEquals("a/b/c/d" , $testRule[0]->getIdentifier(),
									"Exact match takes precedence 2");


		// Test wildcard tail matching ("a/b/d" => "*/d")
		$this->assertEquals(1, count($rulesManager->match(NULL, "a/b/d")),
									"Wildcard tail matching rule 1"  );

   	$testRule = $rulesManager->match(NULL, "a/b/d");
		$this->assertEquals("*/d", $testRule[0]->getIdentifier(),
									"Wildcard tail matching rule 2" );


		// Test the longest matching pattern rule ("x/c/d")
		$testRule = $rulesManager->match(NULL, "x/c/d");
		$this->assertEquals(1, count($testRule),
						 			"Longest tail rule 1");

		$testRule = $rulesManager->match(NULL, "x/c/d");
		$this->assertEquals("*/c/d", $testRule[0]->getIdentifier(),
									"Longest tail rule 2");

	}


	/**
	* Test the basic stack mechanisms.
	*/
	function test_StackMethods() {

		$digester =& $this->digester;

		$value = NULL;	//  Object

		// New stack must be empty
		$this->assertEquals(0, $digester->getCount(), "New stack is empty");
		$value = $digester->peek();
		$this->assertEquals(NULL, $value, "New stack peek() returns null");
		$value = $digester->pop();
		$this->assertEquals(NULL, $value, "New stack pop() returns null");


		// Test pushing and popping activities

		$dummyObject1 = array('first' => 'First Item');
		$digester->push($dummyObject1); // digester->push(&$object)
		$this->assertEquals(1, $digester->getCount(), "Pushed one item size");
		$value = $digester->peek();
		$this->assert(NULL != $value, "Peeked first item is not null");
		$this->assertEquals("First Item", $value['first'], "Peeked first item value");

		$dummyObject2 = array('second' => 'Second Item');
		$digester->push($dummyObject2);
		$this->assertEquals(2, $digester->getCount(), "Pushed two items size");
		$value = $digester->peek();
		$this->assert(NULL != $value, "Peeked second item is not null");
		$this->assertEquals("Second Item", $value['second'], "Peeked second item value");

		$value = $digester->pop();
		$this->assertEquals(1, $digester->getCount(), "Popped stack size");
		$this->assert(NULL != $value, "Popped second item is not null");
		$this->assertEquals("Second Item", $value['second'], "Popped second item value");
		$value = $digester->peek();
		$this->assert(NULL != $value, "Remaining item is not null");
		$this->assertEquals("First Item", $value['first'], "Remaining item value");
		$this->assertEquals( 1, $digester->getCount(), "Remaining stack size");


		// Cleared stack is empty
		
		$dummyObject3 = array('dummy' => 'Dummy Item');
		$digester->push($dummyObject3);
		$this->assertEquals(2, $digester->getCount(), "Two items on the stack");
		$digester->clear();
		$this->assertEquals(0, $digester->getCount(), "Cleared stack is empty");
		$value = $digester->peek();
		$this->assert(NULL == $value, "Cleared stack peek() returns null");
		$value = $digester->pop();
		$this->assert(NULL == $value, "Cleared stack pop() returns null");

	}

}
?>